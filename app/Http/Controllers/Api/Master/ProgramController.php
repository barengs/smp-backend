<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgramController extends Controller
{
    /**
     * Menampilkan daftar semua program studi
     *
     * Method ini digunakan untuk mengambil semua data program studi dari database.
     * Program studi mencakup berbagai program pendidikan yang ditawarkan pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 [
     *   {
     *     "id": 1,
     *     "name": "Program Tahfidz",
     *     "description": "Program menghafal Al-Quran",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "name": "Program Kitab Kuning",
     *     "description": "Program kajian kitab kuning",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * ]
     *
     * @response 500 {
     *   "error": "Failed to fetch programs"
     * }
     */
    public function index()
    {
        try {
            $programs = Program::with('hostels')->get();
            return response()->json($programs, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch programs'], 500);
        } catch (ModelNotFoundException $th) {
            return response()->json(['error' => 'Model not found'], 404);
        }
    }

    /**
     * Menyimpan program studi baru
     *
     * Method ini digunakan untuk membuat program studi baru dengan validasi input
     * yang ketat.
     *
     * @group Master Data
     * @authenticated
     *
     * @bodyParam name string required Nama program studi (maksimal 255 karakter). Example: Program Tahfidz
     * @bodyParam description string Deskripsi program studi. Example: Program menghafal Al-Quran
     *
     * @response 201 {
     *   "message": "Program created successfully",
     *   "status": 201,
     *   "data": {
     *     "id": 1,
     *     "name": "Program Tahfidz",
     *     "description": "Program menghafal Al-Quran",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Validation error: The name field is required."
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            $program = Program::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            return new ProgramResource('Program created successfully', $program, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No data found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error: ' . $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail program studi berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail program studi spesifik berdasarkan ID.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID program studi yang akan ditampilkan. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Program Tahfidz",
     *   "description": "Program menghafal Al-Quran",
     *   "created_at": "2024-01-01T00:00:00.000000Z",
     *   "updated_at": "2024-01-01T00:00:00.000000Z"
     * }
     *
     * @response 404 {
     *   "error": "Program not found"
     * }
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Mengupdate data program studi yang ada
     *
     * Method ini digunakan untuk mengubah data program studi yang sudah ada
     * dengan validasi input yang ketat.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID program studi yang akan diupdate. Example: 1
     * @bodyParam name string required Nama program studi (maksimal 255 karakter). Example: Program Tahfidz Updated
     * @bodyParam description string Deskripsi program studi. Example: Program menghafal Al-Quran
     *
     * @response 200 {
     *   "message": "Program updated successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "name": "Program Tahfidz Updated",
     *     "description": "Program menghafal Al-Quran",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "No data found"
     * }
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            $program = Program::findOrFail($id);
            $program->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            return new ProgramResource('Program updated successfully', $program, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No data found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error: ' . $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Menghapus program studi berdasarkan ID
     *
     * Method ini digunakan untuk menghapus program studi berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan program studi harus dilakukan dengan hati-hati
     * karena dapat mempengaruhi data santri yang terkait.
     *
     * @group Master Data
     * @authenticated
     *
     * @urlParam id integer required ID program studi yang akan dihapus. Example: 1
     *
     * @response 200 {
     *   "message": "Program deleted successfully",
     *   "status": 200
     * }
     *
     * @response 404 {
     *   "message": "Program not found"
     * }
     */
    public function destroy(string $id)
    {
        //
    }
}
