<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\ClassGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassGroupResource;

class ClassGroupController extends Controller
{
    /**
     * Menampilkan daftar semua kelompok kelas
     *
     * Method ini digunakan untuk mengambil semua data kelompok kelas dari database
     * beserta relasi classroom. Kelompok kelas mencakup pembagian siswa
     * dalam satu ruang kelas.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Kelas 7A",
     *       "classroom_id": 1,
     *       "classroom": {
     *         "id": 1,
     *         "name": "Ruang Kelas 7A",
     *         "description": "Ruang kelas untuk siswa kelas 7A"
     *       },
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "error": "Failed to retrieve class groups"
     * }
     */
    public function index()
    {
        try {
            $classGroups = ClassGroup::with('classroom')->get();
            return new ClassGroupResource($classGroups);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve class groups'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        try {
            $classGroup = ClassGroup::create([
                'name' => $request->name,
                'classroom_id' => $request->classroom_id,
            ]);
            return new ClassGroupResource($classGroup);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create class group'], 500);
        }
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
