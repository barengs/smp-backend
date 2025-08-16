<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @group Bank Santri - Account Movement Management
 *
 * API untuk mengelola pergerakan keuangan (movements) pada akun tabungan santri.
 * Endpoint ini menangani pencatatan transaksi keuangan, setoran, penarikan, dan transfer antar akun.
 * Setiap movement akan otomatis mengupdate saldo akun dan mencatat riwayat transaksi.
 *
 * @authenticated
 */
class AccountMovementController extends Controller
{
    /**
     * Daftar Semua Movement
     *
     * Menampilkan daftar semua pergerakan keuangan dalam sistem.
     * Dapat difilter berdasarkan akun, tanggal, dan jenis transaksi.
     *
     * @OA\Get(
     *     path="/api/account-movement",
     *     operationId="getAccountMovements",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Daftar Semua Movement",
     *     description="Menampilkan daftar semua pergerakan keuangan dengan filter dan pagination",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="account_number",
     *         in="query",
     *         description="Filter berdasarkan nomor akun",
     *         required=false,
     *         @OA\Schema(type="string", example="20250197001")
     *     ),
     *     @OA\Parameter(
     *         name="transaction_type",
     *         in="query",
     *         description="Filter berdasarkan jenis transaksi",
     *         required=false,
     *         @OA\Schema(type="string", enum={"CREDIT","DEBIT","TRANSFER"}, example="CREDIT")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter tanggal mulai (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter tanggal akhir (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Jumlah item per halaman",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar movements",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementListResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @queryParam account_number string Filter berdasarkan nomor akun. Example: 20250197001
     * @queryParam transaction_type string Filter berdasarkan jenis transaksi (CREDIT/DEBIT/TRANSFER). Example: CREDIT
     * @queryParam start_date string Filter tanggal mulai (YYYY-MM-DD). Example: 2025-01-01
     * @queryParam end_date string Filter tanggal akhir (YYYY-MM-DD). Example: 2025-01-31
     * @queryParam per_page integer Jumlah item per halaman. Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "account_number": "20250197001",
     *       "transaction_id": "uuid-string",
     *       "movement_time": "2025-01-15T10:30:00.000000Z",
     *       "description": "Setoran awal",
     *       "debit_amount": "0.00",
     *       "credit_amount": "100000.00",
     *       "balance_after_movement": "100000.00",
     *       "account": {
     *         "account_number": "20250197001",
     *         "customer": {
     *           "name": "Ahmad Santoso"
     *         }
     *       },
     *       "transaction": {
     *         "transaction_type": {
     *           "name": "Setoran Tunai"
     *         }
     *       }
     *     }
     *   ],
     *   "pagination": {
     *     "current_page": 1,
     *     "per_page": 15,
     *     "total": 100
     *   }
     * }
     */
    public function index(Request $request)
    {
        $query = AccountMovement::with(['account.customer', 'transaction.transactionType']);

        // Filter by account number
        if ($request->filled('account_number')) {
            $query->where('account_number', $request->account_number);
        }

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->whereHas('transaction.transactionType', function ($q) use ($request) {
                $q->where('type', $request->transaction_type);
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('movement_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('movement_time', '<=', $request->end_date);
        }

        // Order by movement time (newest first)
        $query->orderBy('movement_time', 'desc');

        $perPage = $request->get('per_page', 15);
        $movements = $query->paginate($perPage);

        return response()->json($movements);
    }

    /**
     * Buat Movement Baru
     *
     * Membuat pergerakan keuangan baru (setoran, penarikan, atau transfer).
     * Sistem akan otomatis mengupdate saldo akun dan mencatat transaksi.
     *
     * @OA\Post(
     *     path="/api/account-movement",
     *     operationId="createAccountMovement",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Buat Movement Baru",
     *     description="Membuat pergerakan keuangan baru dengan validasi dan update saldo otomatis",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data untuk membuat movement baru",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movement berhasil dibuat",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementCreateResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - data tidak lengkap",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Account tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - saldo tidak mencukupi",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @bodyParam account_number string required Nomor akun tabungan. Example: 20250197001
     * @bodyParam transaction_type_id integer required ID jenis transaksi. Example: 1
     * @bodyParam amount decimal required Jumlah transaksi (positif untuk setoran, negatif untuk penarikan). Example: 100000.00
     * @bodyParam description string required Deskripsi transaksi. Example: Setoran awal
     * @bodyParam reference_number string optional Nomor referensi eksternal. Example: REF001
     * @bodyParam channel string optional Channel transaksi (CASH, TRANSFER, MOBILE). Example: CASH
     * @bodyParam destination_account string optional Nomor akun tujuan (untuk transfer). Example: 20250197002
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "account_number": "20250197001",
     *     "transaction_id": "uuid-string",
     *     "movement_time": "2025-01-15T10:30:00.000000Z",
     *     "description": "Setoran awal",
     *     "debit_amount": "0.00",
     *     "credit_amount": "100000.00",
     *     "balance_after_movement": "100000.00"
     *   },
     *   "message": "Movement created successfully"
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|exists:accounts,account_number',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'channel' => 'nullable|string|in:CASH,TRANSFER,MOBILE',
            'destination_account' => 'nullable|exists:accounts,account_number',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

            $account = Account::findOrFail($request->account_number);

            // Check if account is active
            if ($account->status !== 'AKTIF') {
                return response()->json([
                    'message' => 'Account is not active. Current status: ' . $account->status
                ], 409);
            }

            $amount = $request->amount;
            $isCredit = $amount > 0;
            $isDebit = $amount < 0;

            // For debit transactions, check if balance is sufficient
            if ($isDebit && abs($amount) > $account->balance) {
                return response()->json([
                    'message' => 'Insufficient balance. Available: ' . $account->balance . ', Required: ' . abs($amount)
                ], 409);
            }

            // Create transaction record
            $transaction = Transaction::create([
                'id' => Str::uuid(),
                'transaction_type_id' => $request->transaction_type_id,
                'description' => $request->description,
                'amount' => abs($amount),
                'status' => 'SUCCESS',
                'reference_number' => $request->reference_number,
                'channel' => $request->channel ?? 'CASH',
                'source_account' => $request->account_number,
                'destination_account' => $request->destination_account,
            ]);

            // Calculate new balance
            $newBalance = $account->balance + $amount;

            // Create account movement
            $movement = AccountMovement::create([
                'account_number' => $request->account_number,
                'transaction_id' => $transaction->id,
                'movement_time' => now(),
                'description' => $request->description,
                'debit_amount' => $isDebit ? abs($amount) : 0,
                'credit_amount' => $isCredit ? $amount : 0,
                'balance_after_movement' => $newBalance,
            ]);

            // Update account balance
            $account->update(['balance' => $newBalance]);

            // If this is a transfer, create movement for destination account
            if ($request->filled('destination_account') && $isDebit) {
                $destinationAccount = Account::findOrFail($request->destination_account);

                if ($destinationAccount->status !== 'AKTIF') {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Destination account is not active'
                    ], 409);
                }

                // Create destination account movement
                AccountMovement::create([
                    'account_number' => $request->destination_account,
                    'transaction_id' => $transaction->id,
                    'movement_time' => now(),
                    'description' => 'Transfer dari ' . $request->account_number . ' - ' . $request->description,
                    'debit_amount' => 0,
                    'credit_amount' => abs($amount),
                    'balance_after_movement' => $destinationAccount->balance + abs($amount),
                ]);

                // Update destination account balance
                $destinationAccount->update([
                    'balance' => $destinationAccount->balance + abs($amount)
                ]);
            }

            DB::commit();

            return response()->json([
                'data' => $movement->load(['account.customer', 'transaction.transactionType']),
                'message' => 'Movement created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create movement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail Movement
     *
     * Menampilkan detail lengkap pergerakan keuangan berdasarkan ID.
     *
     * @OA\Get(
     *     path="/api/account-movement/{id}",
     *     operationId="getAccountMovement",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Detail Movement",
     *     description="Menampilkan detail lengkap pergerakan keuangan dengan relasi account dan transaction",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID movement",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail movement",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementSingleResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movement tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @urlParam id integer required ID movement. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "account_number": "20250197001",
     *     "transaction_id": "uuid-string",
     *     "movement_time": "2025-01-15T10:30:00.000000Z",
     *     "description": "Setoran awal",
     *     "debit_amount": "0.00",
     *     "credit_amount": "100000.00",
     *     "balance_after_movement": "100000.00",
     *     "account": {
     *       "account_number": "20250197001",
     *       "balance": "100000.00",
     *       "customer": {
     *         "name": "Ahmad Santoso",
     *         "nis": "20250197001"
     *       }
     *     },
     *     "transaction": {
     *       "transaction_type": {
     *         "name": "Setoran Tunai",
     *         "type": "CREDIT"
     *       },
     *       "channel": "CASH",
     *       "reference_number": "REF001"
     *     }
     *   }
     * }
     */
    public function show(string $id)
    {
        try {
            $movement = AccountMovement::with([
                'account.customer',
                'transaction.transactionType'
            ])->findOrFail($id);

            return response()->json(['data' => $movement]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Movement not found'], 404);
        }
    }

    /**
     * Update Movement
     *
     * Memperbarui informasi movement (hanya deskripsi yang dapat diubah).
     * Amount dan balance tidak dapat diubah untuk menjaga integritas data.
     *
     * @OA\Put(
     *     path="/api/account-movement/{id}",
     *     operationId="updateAccountMovement",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Update Movement",
     *     description="Memperbarui deskripsi movement (amount dan balance tidak dapat diubah)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID movement",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data untuk update movement",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movement berhasil diupdate",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementUpdateResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - data tidak lengkap",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movement tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     *
     * @urlParam id integer required ID movement. Example: 1
     * @bodyParam description string required Deskripsi movement yang baru. Example: Setoran awal bulan
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "account_number": "20250197001",
     *     "description": "Setoran awal bulan",
     *     "debit_amount": "0.00",
     *     "credit_amount": "100000.00",
     *     "balance_after_movement": "100000.00"
     *   },
     *   "message": "Movement updated successfully"
     * }
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $movement = AccountMovement::findOrFail($id);

            // Only allow description update for data integrity
            $movement->update([
                'description' => $request->description
            ]);

            return response()->json([
                'data' => $movement,
                'message' => 'Movement updated successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Movement not found'], 404);
        }
    }

    /**
     * Hapus Movement
     *
     * Menghapus movement dari sistem. Hanya movement yang belum mempengaruhi saldo yang dapat dihapus.
     * Movement yang sudah mempengaruhi saldo tidak dapat dihapus untuk menjaga integritas data.
     *
     * @OA\Delete(
     *     path="/api/account-movement/{id}",
     *     operationId="deleteAccountMovement",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Hapus Movement",
     *     description="Menghapus movement (hanya jika belum mempengaruhi saldo)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID movement yang akan dihapus",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Movement berhasil dihapus"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movement tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - movement tidak dapat dihapus",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @urlParam id integer required ID movement yang akan dihapus. Example: 1
     *
     * @response 204
     *
     * @response 409 {
     *   "message": "Cannot delete movement that affects account balance"
     * }
     */
    public function destroy(string $id)
    {
        try {
            $movement = AccountMovement::findOrFail($id);

            // Check if movement affects balance (has debit or credit amount)
            if ($movement->debit_amount > 0 || $movement->credit_amount > 0) {
                return response()->json([
                    'message' => 'Cannot delete movement that affects account balance'
                ], 409);
            }

            $movement->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Movement not found'], 404);
        }
    }

    /**
     * Riwayat Transaksi Akun
     *
     * Menampilkan riwayat lengkap transaksi untuk akun tertentu.
     * Dapat difilter berdasarkan periode waktu dan jenis transaksi.
     *
     * @OA\Get(
     *     path="/api/account-movement/account/{account_number}/history",
     *     operationId="getAccountTransactionHistory",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Riwayat Transaksi Akun",
     *     description="Menampilkan riwayat lengkap transaksi untuk akun tertentu",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="account_number",
     *         in="path",
     *         required=true,
     *         description="Nomor akun tabungan",
     *         @OA\Schema(type="string", example="20250197001")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter tanggal mulai (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter tanggal akhir (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Jumlah item per halaman",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan riwayat transaksi",
     *         @OA\JsonContent(ref="#/components/schemas/AccountMovementHistoryResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Account tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @urlParam account_number string required Nomor akun tabungan. Example: 20250197001
     * @queryParam start_date string Filter tanggal mulai (YYYY-MM-DD). Example: 2025-01-01
     * @queryParam end_date string Filter tanggal akhir (YYYY-MM-DD). Example: 2025-01-31
     * @queryParam per_page integer Jumlah item per halaman. Example: 15
     *
     * @response 200 {
     *   "data": {
     *     "account": {
     *       "account_number": "20250197001",
     *       "balance": "150000.00",
     *       "customer": {
     *         "name": "Ahmad Santoso"
     *       }
     *     },
     *     "movements": [
     *       {
     *         "id": 1,
     *         "movement_time": "2025-01-15T10:30:00.000000Z",
     *         "description": "Setoran awal",
     *         "debit_amount": "0.00",
     *         "credit_amount": "100000.00",
     *         "balance_after_movement": "100000.00"
     *       }
     *     ],
     *     "summary": {
     *       "total_credit": "150000.00",
     *       "total_debit": "0.00",
     *       "transaction_count": 1
     *     }
     *   }
     * }
     */
    public function accountHistory(Request $request, string $account_number)
    {
        try {
            $account = Account::with('customer')->findOrFail($account_number);

            $query = AccountMovement::where('account_number', $account_number);

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('movement_time', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('movement_time', '<=', $request->end_date);
            }

            // Order by movement time (newest first)
            $query->orderBy('movement_time', 'desc');

            $perPage = $request->get('per_page', 15);
            $movements = $query->paginate($perPage);

            // Calculate summary
            $summary = [
                'total_credit' => $movements->sum('credit_amount'),
                'total_debit' => $movements->sum('debit_amount'),
                'transaction_count' => $movements->total()
            ];

            return response()->json([
                'data' => [
                    'account' => $account,
                    'movements' => $movements,
                    'summary' => $summary
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    /**
     * Rekap Transaksi Harian
     *
     * Menampilkan rekap transaksi harian untuk periode tertentu.
     * Berguna untuk laporan keuangan dan monitoring.
     *
     * @OA\Get(
     *     path="/api/account-movement/daily-summary",
     *     operationId="getDailyTransactionSummary",
     *     tags={"Bank Santri - Account Movement Management"},
     *     summary="Rekap Transaksi Harian",
     *     description="Menampilkan rekap transaksi harian untuk periode tertentu",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=true,
     *         description="Tanggal mulai (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=true,
     *         description="Tanggal akhir (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan rekap harian",
     *         @OA\JsonContent(ref="#/components/schemas/DailySummaryResponse")
     *     )
     * )
     *
     * @queryParam start_date string required Tanggal mulai (YYYY-MM-DD). Example: 2025-01-01
     * @queryParam end_date string required Tanggal akhir (YYYY-MM-DD). Example: 2025-01-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "date": "2025-01-15",
     *       "total_credit": "500000.00",
     *       "total_debit": "200000.00",
     *       "net_amount": "300000.00",
     *       "transaction_count": 25
     *     }
     *   ]
     * }
     */
    public function dailySummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $summary = AccountMovement::selectRaw('
                DATE(movement_time) as date,
                SUM(credit_amount) as total_credit,
                SUM(debit_amount) as total_debit,
                COUNT(*) as transaction_count
            ')
            ->whereBetween('movement_time', [$request->start_date, $request->end_date . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->net_amount = $item->total_credit - $item->total_debit;
                return $item;
            });

        return response()->json(['data' => $summary]);
    }
}
