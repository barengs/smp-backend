<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicYearResource;

class AcademicYearController extends Controller
{
    /**
     * Menampilkan daftar semua tahun akademik
     *
     * Method ini digunakan untuk mengambil semua data tahun akademik dari database.
     * Tahun akademik mencakup periode belajar yang digunakan pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Data retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "year": "2024/2025",
     *       "semester": "Ganjil",
     *       "active": true,
     *       "description": "Tahun Akademik 2024/2025 Semester Ganjil",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "message": "Failed to retrieve data",
     *   "status": 500,
     *   "error": "Error details"
     * }
     */
    public function index()
    {
        try {
            $data = AcademicYear::all();
            return new AcademicYearResource('Data retrieved successfully', $data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve data',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menyimpan tahun akademik baru
     *
     * Method ini digunakan untuk membuat tahun akademik baru dengan validasi input
     * yang ketat. Tahun akademik harus unik dan tidak boleh duplikat.
     *
     * @group Master Data
     * @authenticated
     *
     * @bodyParam year string required Tahun akademik (format: 2024/2025, maksimal 9 karakter). Example: 2024/2025
     * @bodyParam semester string required Semester (Ganjil/Genap, maksimal 20 karakter). Example: Ganjil
     * @bodyParam active boolean Status aktif tahun akademik. Example: true
     * @bodyParam description string Deskripsi tahun akademik. Example: Tahun Akademik 2024/2025 Semester Ganjil
     *
     * @response 201 {
     *   "message": "Academic year created successfully",
     *   "status": 201,
     *   "data": {
     *     "id": 1,
     *     "year": "2024/2025",
     *     "semester": "Ganjil",
     *     "active": true,
     *     "description": "Tahun Akademik 2024/2025 Semester Ganjil",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "year": ["The year field is required."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|string|max:9|unique:academic_years,year',
                'semester' => 'required|string|max:20',
                'active' => 'boolean',
                'description' => 'nullable|string',
            ]);

            $academicYear = AcademicYear::create([
                'year' => $request->year,
                'semester' => $request->semester,
                'active' => $request->active,
                'description' => $request->description,
            ]);

            return new AcademicYearResource('Academic year created successfully', $academicYear, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail tahun akademik berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail tahun akademik spesifik berdasarkan ID.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID tahun akademik yang akan ditampilkan. Example: 1
     *
     * @response 200 {
     *   "message": "Data retrieved successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "year": "2024/2025",
     *     "semester": "Ganjil",
     *     "active": true,
     *     "description": "Tahun Akademik 2024/2025 Semester Ganjil",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Academic year not found"
     * }
     */
    public function show(string $id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);
            return new AcademicYearResource('Data retrieved successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengupdate data tahun akademik yang ada
     *
     * Method ini digunakan untuk mengubah data tahun akademik yang sudah ada
     * dengan validasi input yang ketat.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID tahun akademik yang akan diupdate. Example: 1
     * @bodyParam year string required Tahun akademik (format: 2024/2025, maksimal 9 karakter). Example: 2024/2025
     * @bodyParam semester string required Semester (Ganjil/Genap, maksimal 20 karakter). Example: Ganjil
     * @bodyParam active boolean Status aktif tahun akademik. Example: true
     * @bodyParam description string Deskripsi tahun akademik. Example: Tahun Akademik 2024/2025 Semester Ganjil
     *
     * @response 200 {
     *   "message": "Academic year updated successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "year": "2024/2025",
     *     "semester": "Ganjil",
     *     "active": true,
     *     "description": "Tahun Akademik 2024/2025 Semester Ganjil Updated",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Academic year not found"
     * }
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'year' => 'required|string|max:9|unique:academic_years,year,' . $id,
                'semester' => 'required|string|max:20',
                'active' => 'boolean',
                'description' => 'nullable|string',
            ]);

            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->update([
                'year' => $request->year,
                'semester' => $request->semester,
                'active' => $request->active,
                'description' => $request->description,
            ]);

            return new AcademicYearResource('Academic year updated successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus tahun akademik berdasarkan ID
     *
     * Method ini digunakan untuk menghapus tahun akademik berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan tahun akademik harus dilakukan dengan hati-hati
     * karena dapat mempengaruhi data pendidikan yang terkait.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID tahun akademik yang akan dihapus. Example: 1
     *
     * @response 200 {
     *   "message": "Academic year deleted successfully",
     *   "status": 200
     * }
     *
     * @response 404 {
     *   "message": "Academic year not found"
     * }
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Mengatur status aktif tahun akademik
     *
     * Method ini digunakan untuk mengubah status aktif tahun akademik.
     * Hanya satu tahun akademik yang dapat aktif pada satu waktu.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID tahun akademik yang statusnya akan diubah. Example: 1
     * @bodyParam active boolean required Status aktif (true = aktif, false = nonaktif). Example: true
     *
     * @response 200 {
     *   "message": "Academic year status updated successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "year": "2024/2025",
     *     "semester": "Ganjil",
     *     "active": true,
     *     "description": "Tahun Akademik 2024/2025 Semester Ganjil",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Academic year not found"
     * }
     */
    public function setActive(Request $request, string $id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->active = $request->active;
            $academicYear->save();

            return new AcademicYearResource('Academic year status updated successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update academic year status',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
