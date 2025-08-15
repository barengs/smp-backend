<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Bank Santri - Account Management
 *
 * API untuk mengelola akun tabungan santri dalam sistem Bank Santri.
 * Endpoint ini memungkinkan admin untuk membuat, mengelola, dan memantau akun tabungan santri.
 *
 * @authenticated
 */
class AccountController extends Controller
{
    /**
     * Daftar Semua Akun
     *
     * Menampilkan daftar semua akun tabungan santri yang ada dalam sistem.
     * Response akan menyertakan data customer (siswa) dan product (produk keuangan).
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "account_number": "20250197001",
     *       "customer_id": 1,
     *       "product_id": 1,
     *       "balance": "150000.00",
     *       "status": "ACTIVE",
     *       "open_date": "2025-01-15",
     *       "close_date": null,
     *       "customer": {
     *         "id": 1,
     *         "name": "Ahmad Santoso",
     *         "nis": "20250197001"
     *       },
     *       "product": {
     *         "id": 1,
     *         "name": "Tabungan Santri",
     *         "type": "SAVINGS"
     *       }
     *     }
     *   ]
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     *
     * @response 403 {
     *   "message": "Unauthorized action."
     * }
     */
    public function index()
    {
        $accounts = Account::with(['customer', 'product'])->get();
        return response()->json($accounts);
    }

    /**
     * Buat Akun Baru
     *
     * Membuat akun tabungan baru untuk santri. Sistem akan otomatis generate nomor akun
     * berdasarkan NIS siswa dan mengatur status awal sebagai INACTIVE.
     *
     * @bodyParam student_id integer required ID siswa yang akan dibuatkan akun. Example: 1
     * @bodyParam product_id integer required ID produk keuangan yang dipilih. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "account_number": "20250197001",
     *     "customer_id": 1,
     *     "product_id": 1,
     *     "balance": "0.00",
     *     "status": "INACTIVE",
     *     "open_date": "2025-01-15T10:30:00.000000Z",
     *     "close_date": null
     *   },
     *   "message": "Account created successfully"
     * }
     *
     * @response 400 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "student_id": ["The student id field is required."],
     *     "product_id": ["The product id field is required."]
     *   }
     * }
     *
     * @response 409 {
     *   "message": "Student already has an account"
     * }
     *
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "student_id": ["The selected student id is invalid."],
     *     "product_id": ["The selected product id is invalid."]
     *   }
     * }
     *
     * @response 500 {
     *   "message": "Failed to create account",
     *   "error": "Error details"
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $student = Student::findOrFail($request->student_id);
            // Check if the student already has an account
            if (Account::where('customer_id', $student->id)->exists()) {
                return response()->json(['message' => 'Student already has an account'], 409);
            }

            // Create a new account for the student
            $account = Account::create([
                'account_number' => $student->nis,
                'customer_id' => $student->id,
                'product_id' => $request->product_id,
                'balance' => 0,
                'status' => 'INACTIVE',
                'open_date' => now(),
            ]);

            return response()->json($account, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create account', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Detail Akun
     *
     * Menampilkan detail lengkap akun tabungan berdasarkan nomor akun.
     * Response akan menyertakan data customer, product, dan riwayat movements (transaksi).
     *
     * @urlParam id string required Nomor akun tabungan. Example: 20250197001
     *
     * @response 200 {
     *   "data": {
     *     "account_number": "20250197001",
     *     "customer_id": 1,
     *     "product_id": 1,
     *     "balance": "150000.00",
     *     "status": "ACTIVE",
     *     "open_date": "2025-01-15",
     *     "close_date": null,
     *     "customer": {
     *       "id": 1,
     *       "name": "Ahmad Santoso",
     *       "nis": "20250197001",
     *       "class": "VII A"
     *     },
     *     "product": {
     *       "id": 1,
     *       "name": "Tabungan Santri",
     *       "type": "SAVINGS",
     *       "description": "Produk tabungan khusus untuk santri"
     *     },
     *     "movements": [
     *       {
     *         "id": 1,
     *         "transaction_type": "CREDIT",
     *         "amount": "100000.00",
     *         "description": "Setoran awal",
     *         "created_at": "2025-01-15T10:30:00.000000Z"
     *       }
     *     ]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Account not found"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function show(string $id)
    {
        try {
            $account = Account::with(['customer', 'product', 'movements'])->findOrFail($id);
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Update Akun
     *
     * Memperbarui informasi akun tabungan seperti produk keuangan dan status akun.
     * Hanya field yang diizinkan yang dapat diupdate.
     *
     * @urlParam id string required Nomor akun tabungan. Example: 20250197001
     * @bodyParam product_id integer required ID produk keuangan baru. Example: 2
     * @bodyParam status string required Status akun baru. Example: ACTIVE
     *
     * @response 200 {
     *   "data": {
     *     "account_number": "20250197001",
     *     "customer_id": 1,
     *     "product_id": 2,
     *     "balance": "150000.00",
     *     "status": "ACTIVE",
     *     "open_date": "2025-01-15",
     *     "close_date": null
     *   },
     *   "message": "Account updated successfully"
     * }
     *
     * @response 400 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "product_id": ["The product id field is required."],
     *     "status": ["The status field is required."]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Account not found"
     * }
     *
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "product_id": ["The selected product id is invalid."],
     *     "status": ["The selected status is invalid."]
     *   }
     * }
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'status' => 'required|in:ACTIVE,DORMANT,CLOSED,BLOCKED,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $account = Account::findOrFail($id);
            $account->update($request->only(['product_id', 'status']));
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Hapus Akun
     *
     * Menghapus akun tabungan dari sistem. Operasi ini tidak dapat dibatalkan.
     * Pastikan semua transaksi telah diselesaikan sebelum menghapus akun.
     *
     * @urlParam id string required Nomor akun tabungan yang akan dihapus. Example: 20250197001
     *
     * @response 204
     *
     * @response 404 {
     *   "message": "Account not found"
     * }
     *
     * @response 409 {
     *   "message": "Cannot delete account with active balance or transactions"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function destroy(string $id)
    {
        try {
            $account = Account::findOrFail($id);

            // Check if account can be deleted
            if ($account->balance > 0) {
                return response()->json(['message' => 'Cannot delete account with active balance'], 409);
            }

            if ($account->movements()->exists()) {
                return response()->json(['message' => 'Cannot delete account with transaction history'], 409);
            }

            $account->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Update Status Akun
     *
     * Memperbarui status akun tabungan tanpa mengubah informasi lainnya.
     * Status yang tersedia: ACTIVE, DORMANT, CLOSED, BLOCKED, INACTIVE.
     *
     * @urlParam id string required Nomor akun tabungan. Example: 20250197001
     * @bodyParam status string required Status akun baru. Example: ACTIVE
     *
     * @response 200 {
     *   "data": {
     *     "account_number": "20250197001",
     *     "customer_id": 1,
     *     "product_id": 1,
     *     "balance": "150000.00",
     *     "status": "ACTIVE",
     *     "open_date": "2025-01-15",
     *     "close_date": null
     *   },
     *   "message": "Account status updated successfully"
     * }
     *
     * @response 400 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "status": ["The status field is required."]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Account not found"
     * }
     *
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "status": ["The selected status is invalid."]
     *   }
     * }
     *
     * @response 409 {
     *   "message": "Cannot change status to CLOSED with active balance"
     * }
     */
    public function updateStatus(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:ACTIVE,DORMANT,CLOSED,BLOCKED,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $account = Account::findOrFail($id);

            // Additional validation for status changes
            if ($request->status === 'CLOSED' && $account->balance > 0) {
                return response()->json(['message' => 'Cannot change status to CLOSED with active balance'], 409);
            }

            $account->status = $request->status;

            // Set close_date if status is CLOSED
            if ($request->status === 'CLOSED') {
                $account->close_date = now();
            } else {
                $account->close_date = null;
            }

            $account->save();
            return response()->json($account);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }
}
