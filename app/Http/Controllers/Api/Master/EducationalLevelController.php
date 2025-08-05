<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EducationalLevelController extends Controller
{
    /**
     * Menampilkan daftar semua tingkat pendidikan
     *
     * Method ini digunakan untuk mengambil semua data tingkat pendidikan dari database.
     * Tingkat pendidikan mencakup jenjang pendidikan yang ditawarkan pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Data educational level",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "SMP",
     *       "description": "Sekolah Menengah Pertama",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "SMA",
     *       "description": "Sekolah Menengah Atas",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "Data tidak ditemukan"
     * }
     */
    public function index()
    {
        // $data = EducationalLevel::all();

        // if ($data->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Data tidak ditemukan',
        //     ], 404);
        // }
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data educational level',
        //     'data' => $data,
        // ]);
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
