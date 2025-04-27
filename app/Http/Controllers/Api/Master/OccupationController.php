<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Occupation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OccupationResource;
use Illuminate\Validation\ValidationException;

class OccupationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch all occupations from the database
            $occupations = Occupation::all();
            // Return a successful response with the occupations
            return new OccupationResource('Occupations retrieved successfully', $occupations, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch occupations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Create a new occupation using the validated data
            $occupation = Occupation::create($request->all());

            // Return a successful response with the created occupation
            return new OccupationResource('Occupation created successfully', $occupation, 201);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Return a successful response with the occupation
            return new OccupationResource('Occupation retrieved successfully', $occupation, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Update the occupation with the validated data
            $occupation->update($request->all());

            // Return a successful response with the updated occupation
            return new OccupationResource('Occupation updated successfully', $occupation, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Delete the occupation
            $occupation->delete();

            // Return a successful response indicating deletion
            return new OccupationResource('Occupation deleted successfully', null, 204);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
