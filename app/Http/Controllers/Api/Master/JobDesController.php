<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobDesController extends Controller
{
    /**
     * Menampilkan daftar semua job description
     *
     * Method ini digunakan untuk mengambil semua data job description dari database.
     * Job description mencakup deskripsi tugas dan tanggung jawab
     * untuk berbagai posisi di pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Job descriptions retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Guru Mata Pelajaran",
     *       "description": "Bertanggung jawab mengajar mata pelajaran tertentu",
     *       "requirements": "Sarjana pendidikan sesuai mata pelajaran",
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
