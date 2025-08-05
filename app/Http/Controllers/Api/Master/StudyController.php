<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Study;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudyResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudyController extends Controller
{
    /**
     * Menampilkan daftar semua mata pelajaran
     *
     * Method ini digunakan untuk mengambil semua data mata pelajaran dari database.
     * Mata pelajaran mencakup kurikulum yang diajarkan di pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Studies retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Matematika",
     *       "description": "Mata pelajaran matematika untuk tingkat SMP",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Bahasa Indonesia",
     *       "description": "Mata pelajaran bahasa Indonesia",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        // Fetch all studies
        $studies = Study::all();

        // Return the studies as a resource collection
        return new StudyResource('Studies retrieved successfully', $studies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Create a new study
            $study = Study::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            // Return the created study as a resource
            return new StudyResource('Study created successfully', $study, 201);
        } catch (ValidationException $er) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $er->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the study by ID
            $study = Study::findOrFail($id);

            // Return the study as a resource
            return new StudyResource('Study retrieved successfully', $study, 200);
        } catch (\Exception $e) {
            return response()->json('An error occurred: ' . $e->getMessage(), 500);
        } catch (ModelNotFoundException $e) {
            return response()->json('Study not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $study = Study::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);
            // Update the study
            $study->update($validated);
            // Return the updated study as a resource
            return new StudyResource('Study updated successfully', $study, 200);
        } catch (ValidationException $er) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $er->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('Study not found', 404);
        } catch (\Exception $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the study by ID
            $study = Study::findOrFail($id);

            // Delete the study
            $study->delete();

            // Return a success message
            return response()->json('Study deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('Study not found', 404);
        } catch (\Exception $e) {
            return response()->json('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
