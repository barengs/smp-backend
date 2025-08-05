<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravolt\Indonesia\Models\Province;

class ProvinceController extends Controller
{
    /**
     * Menampilkan daftar semua provinsi di Indonesia
     *
     * Method ini digunakan untuk mengambil semua data provinsi dari database
     * menggunakan package Laravolt Indonesia.
     *
     * @group Region Data
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "id": 1,
     *     "code": "11",
     *     "name": "ACEH",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "code": "12",
     *     "name": "SUMATERA UTARA",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * ]
     *
     * @response 500 {
     *   "error": "Failed to fetch provinces"
     * }
     */
    public function index()
    {
        try {
            $provinces = Province::all();
            return response()->json($provinces, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch provinces'], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json(['error' => 'Model not found'], 404);
        }
    }

    /**
     * Menyimpan provinsi baru
     *
     * Method ini digunakan untuk membuat provinsi baru (tidak digunakan karena data provinsi
     * sudah tersedia dari package Laravolt Indonesia).
     *
     * @group Region Data
     * @authenticated
     *
     * @bodyParam code string required Kode provinsi. Example: 35
     * @bodyParam name string required Nama provinsi. Example: JAWA TIMUR
     *
     * @response 201 {
     *   "message": "Province created successfully",
     *   "data": {
     *     "id": 35,
     *     "code": "35",
     *     "name": "JAWA TIMUR"
     *   }
     * }
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Menampilkan detail provinsi berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail provinsi spesifik berdasarkan ID.
     *
     * @group Region Data
     * @authenticated
     *
     * @urlParam id integer required ID provinsi yang akan ditampilkan. Example: 35
     *
     * @response 200 {
     *   "id": 35,
     *   "code": "35",
     *   "name": "JAWA TIMUR",
     *   "created_at": "2024-01-01T00:00:00.000000Z",
     *   "updated_at": "2024-01-01T00:00:00.000000Z"
     * }
     *
     * @response 404 {
     *   "error": "Province not found"
     * }
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Mengupdate data provinsi yang ada
     *
     * Method ini digunakan untuk mengubah data provinsi yang sudah ada
     * (tidak digunakan karena data provinsi sudah tersedia dari package Laravolt Indonesia).
     *
     * @group Region Data
     * @authenticated
     *
     * @urlParam id integer required ID provinsi yang akan diupdate. Example: 35
     * @bodyParam code string Kode provinsi. Example: 35
     * @bodyParam name string Nama provinsi. Example: JAWA TIMUR
     *
     * @response 200 {
     *   "message": "Province updated successfully",
     *   "data": {
     *     "id": 35,
     *     "code": "35",
     *     "name": "JAWA TIMUR"
     *   }
     * }
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Menghapus provinsi berdasarkan ID
     *
     * Method ini digunakan untuk menghapus provinsi berdasarkan ID
     * (tidak digunakan karena data provinsi sudah tersedia dari package Laravolt Indonesia).
     *
     * @group Region Data
     * @authenticated
     *
     * @urlParam id integer required ID provinsi yang akan dihapus. Example: 35
     *
     * @response 200 {
     *   "message": "Province deleted successfully"
     * }
     */
    public function destroy(string $id)
    {
        //
    }
}
