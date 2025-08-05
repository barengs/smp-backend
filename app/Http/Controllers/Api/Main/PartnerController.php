<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Menampilkan daftar semua partner
     *
     * Method ini digunakan untuk mengambil semua data partner dari database.
     * Partner mencakup mitra kerja pesantren seperti supplier, vendor,
     * dan lembaga mitra lainnya.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Partners retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "PT Supplier Makanan",
     *       "type": "supplier",
     *       "contact_person": "Ahmad Supplier",
     *       "phone": "081234567890",
     *       "email": "supplier@example.com",
     *       "address": "Jl. Supplier No. 123",
     *       "status": "active",
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
