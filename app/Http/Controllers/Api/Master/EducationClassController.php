<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Models\EducationClass;
use App\Http\Controllers\Controller;
use App\Http\Resources\EducationClassResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EducationClassController extends Controller
{
    /**
     * Menampilkan daftar semua kelas pendidikan
     *
     * Method ini digunakan untuk mengambil semua data kelas pendidikan dari database.
     * Kelas pendidikan mencakup pembagian tingkat kelas dalam sistem pendidikan
     * pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Kelas 7",
     *       "description": "Kelas 7 SMP",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Kelas 8",
     *       "description": "Kelas 8 SMP",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "status": false,
     *   "message": "Failed to retrieve data",
     *   "error": "Error details"
     * }
     */
    public function index()
    {
        try {
            $educationClasses = EducationClass::all();
            return new EducationClassResource(
                'Success',
                $educationClasses,
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $th->getMessage(),
            ], 404);
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
