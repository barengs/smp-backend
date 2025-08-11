<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionTypeController extends Controller
{
    /**
     * Menampilkan daftar jenis transaksi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $transactionTypes = TransactionType::paginate($perPage);
            return response()->json($transactionTypes);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve transaction types', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan jenis transaksi baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:transaction_types|max:255',
            'name' => 'required|max:255',
            'category' => 'required|in:transfer,payment,cash_operation,fee',
            'is_debit' => 'required|boolean',
            'is_credit' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $transactionType = TransactionType::create($request->all());
            return response()->json($transactionType, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create transaction type', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan jenis transaksi tertentu.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $transactionType = TransactionType::findOrFail($id);
            return response()->json($transactionType);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction type not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve transaction type', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui jenis transaksi tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|max:255|unique:transaction_types,code,' . $id,
            'name' => 'required|max:255',
            'category' => 'required|in:transfer,payment,cash_operation,fee',
            'is_debit' => 'required|boolean',
            'is_credit' => 'required|boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $transactionType = TransactionType::findOrFail($id);
            $transactionType->update($request->all());
            return response()->json($transactionType);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction type not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update transaction type', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus jenis transaksi tertentu.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $transactionType = TransactionType::findOrFail($id);
            $transactionType->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction type not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete transaction type', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengubah status aktif jenis transaksi.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActiveStatus(string $id)
    {
        try {
            $transactionType = TransactionType::findOrFail($id);
            $transactionType->is_active = !$transactionType->is_active;
            $transactionType->save();

            return response()->json($transactionType);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction type not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update transaction type status', 'error' => $e->getMessage()], 500);
        }
    }
}
