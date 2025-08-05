<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Resources\EducationTypeResource;
use Illuminate\Http\Request;
use App\Models\EducationType;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EducationTypeController extends Controller
{
    /**
     * Menampilkan daftar semua jenis pendidikan
     *
     * Method ini digunakan untuk mengambil semua data jenis pendidikan dari database.
     * Jenis pendidikan mencakup kategori pendidikan yang ditawarkan pesantren.
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
     *       "name": "Formal",
     *       "description": "Pendidikan formal sesuai kurikulum nasional",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Non-Formal",
     *       "description": "Pendidikan non-formal seperti kursus dan pelatihan",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "No data found"
     * }
     */
    public function index()
    {
        try {
            $educationTypes = EducationType::all();
            return new EducationTypeResource('Success', $educationTypes, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No data found'], 404);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            $educationType = EducationType::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return new EducationTypeResource('Education Type created successfully', $educationType, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No data found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error: ' . $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred: ' . $th->getMessage()], 500);
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            $educationType = EducationType::findOrFail($id);
            $educationType->update($validated);

            return new EducationTypeResource('Education Type updated successfully', $educationType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'No data found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error: ' . $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
