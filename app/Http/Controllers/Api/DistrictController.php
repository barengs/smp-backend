<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravolt\Indonesia\Models\District;

class DistrictController extends Controller
{
    /**
     * Menampilkan daftar semua kecamatan di Indonesia
     *
     * Method ini digunakan untuk mengambil semua data kecamatan dari database
     * menggunakan package Laravolt Indonesia beserta relasi kota.
     *
     * @group Region Data
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "id": 1,
     *     "code": "110101",
     *     "name": "KECAMATAN BAKONGAN",
     *     "city_id": 1,
     *     "city": {
     *       "id": 1,
     *       "code": "1101",
     *       "name": "KABUPATEN ACEH SELATAN"
     *     },
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * ]
     *
     * @response 500 {
     *   "error": "Failed to fetch districts"
     * }
     */
    public function index()
    {
        try {
            $districts = District::with('city')->get();
            return response()->json($districts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch districts'], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
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
}
