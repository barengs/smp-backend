<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Education;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\EducationResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $educations = Education::with('education_class')->get();
            return new EducationResource(
                'Success',
                $educations,
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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'education_class_id' => 'required',
                'education_class_id.*' => 'exists:education_classes,id',
            ]);

            $education = Education::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            $education->education_class()->attach($request->education_class_id);
            $education->load('education_class');

            return new EducationResource('Success', $education, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create data',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $education = Education::with('education_class')->findOrFail($id);
            return new EducationResource('Success', $education, 200);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'education_class_id' => 'required|array',
                'education_class_id.*' => 'exists:education_classes,id',
            ]);

            $education = Education::findOrFail($id);
            // Update the education record
            $education->fill($request->only(['name', 'description']));
            $education->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            // Sync the education_class relationship
            $education->education_class()->sync($request->education_class_id);
            $education->load('education_class');

            return new EducationResource(
                'Success',
                $education,
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $education = Education::findOrFail($id);
            $education->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
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
}
