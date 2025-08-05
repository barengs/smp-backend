<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CityController extends Controller
{
    /**
     * Menampilkan daftar semua kota di Indonesia
     *
     * Method ini digunakan untuk mengambil semua data kota dari database
     * menggunakan package Laravolt Indonesia beserta relasi provinsi.
     *
     * @group Region Data
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "id": 1,
     *     "code": "1101",
     *     "name": "KABUPATEN ACEH SELATAN",
     *     "province_id": 1,
     *     "province": {
     *       "id": 1,
     *       "code": "11",
     *       "name": "ACEH"
     *     },
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * ]
     *
     * @response 500 {
     *   "error": "Failed to fetch cities"
     * }
     */
    public function index()
    {
        try {
            $cities = City::with('province')->get();
            return response()->json($cities, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch cities'], 500);
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
}
