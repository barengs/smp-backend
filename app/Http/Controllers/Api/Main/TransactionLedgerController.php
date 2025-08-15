<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionLedgerController extends Controller
{
    /**
     * Menampilkan daftar semua buku besar transaksi
     *
     * Method ini digunakan untuk mengambil semua data buku besar transaksi dari database.
     * Buku besar mencakup detail pencatatan setiap transaksi keuangan
     * untuk audit trail dan laporan keuangan.
     *
     * @group Bank Santri
     * @authenticated
     *
     * @response 200 {
     *   "message": "Transaction ledgers retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "transaction_id": "TRX001",
     *       "coa_code": "ACC001",
     *       "debit_amount": 1000000,
     *       "credit_amount": 0,
     *       "balance": 1000000,
     *       "description": "Setoran awal",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
