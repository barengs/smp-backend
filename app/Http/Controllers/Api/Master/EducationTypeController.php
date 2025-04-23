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
     * Display a listing of the resource.
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
