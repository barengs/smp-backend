<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Profession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProfessionResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch all professions
            $professions = Profession::all();

            // Return the professions as a resource collection
            return new ProfessionResource('Professions retrieved successfully', $professions, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Create a new profession
            $profession = Profession::create([
                'name' => $request->name,
            ]);

            // Return the created profession as a resource
            return new ProfessionResource('Profession created successfully', $profession, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the profession by ID
            $profession = Profession::findOrFail($id);

            // Return the profession as a resource
            return new ProfessionResource('Profession retrieved successfully', $profession, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('Profession not found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Find the profession by ID
            $profession = Profession::findOrFail($id);

            // Update the profession
            $profession->update([
                'name' => $request->name,
            ]);

            // Return the updated profession as a resource
            return new ProfessionResource('Profession updated successfully', $profession, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('Profession not found', 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the profession by ID
            $profession = Profession::findOrFail($id);

            // Delete the profession
            $profession->delete();

            // Return a success message
            return response()->json('Profession deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('Profession not found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }
}
