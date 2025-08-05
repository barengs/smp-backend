<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravolt\Indonesia\Models\Village;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VillageController extends Controller
{
    /**
     * Menampilkan daftar semua desa di Indonesia
     *
     * Method ini digunakan untuk mengambil semua data desa dari database
     * menggunakan package Laravolt Indonesia beserta relasi kecamatan.
     * Data menggunakan pagination untuk performa yang lebih baik.
     *
     * @group Region Data
     * @authenticated
     *
     * @queryParam per_page integer Jumlah data per halaman (default: 5, maksimal: 100). Example: 10
     *
     * @response 200 {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "code": "1101012001",
     *       "name": "DESA BAKONGAN",
     *       "district_id": 1,
     *       "district": {
     *         "id": 1,
     *         "code": "110101",
     *         "name": "KECAMATAN BAKONGAN"
     *       },
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "total": 1000,
     *   "per_page": 5
     * }
     *
     * @response 500 {
     *   "error": "Failed to fetch villages"
     * }
     */
    public function index(Request $request)
    {
        $defaultPerPage = 5;

        $perPage = $request->input('per_page', $defaultPerPage);

        $maxPerPage = 100; // Set a maximum limit for per_page
        if ($perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        try {
            $villages = Village::with('district')->paginate($perPage);
            return response()->json($villages, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch villages'], 500);
        } catch (ModelNotFoundException $th) {
            return response()->json(['error' => 'Model not found'], 404);
        }
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

    /**
     * Menampilkan desa berdasarkan kode kecamatan dari NIK
     *
     * Method ini digunakan untuk mengambil data desa berdasarkan kode kecamatan
     * yang diekstrak dari NIK (Nomor Induk Kependudukan). Kode kecamatan
     * diambil dari 6 digit pertama NIK.
     *
     * @group Region Data
     * @authenticated
     *
     * @urlParam nik string required NIK yang akan digunakan untuk mencari desa. Example: 1101011234567890
     *
     * @response 200 [
     *   {
     *     "id": 1,
     *     "code": "1101012001",
     *     "name": "DESA BAKONGAN",
     *     "district_id": 1,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * ]
     *
     * @response 500 {
     *   "error": "Failed to fetch villages for district"
     * }
     */
    public function villageByNik($nik)
    {
        $distCode = substr($nik, 0, 6); // Ensure the district code is 6 characters long
        try {
            $villages = Village::where('district_code', $distCode)->get();
            return response()->json($villages, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch villages for district'], 500);
        }
    }
}
