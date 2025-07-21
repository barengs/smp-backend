<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ClassroomResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $classroom = Classroom::with('class_groups')->get();
            return new ClassroomResource('Success', $classroom, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
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
                'parent_id' => 'nullable',
                'description' => 'nullable|string|max:255',
            ]);

            $classroom = Classroom::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
            ]);

            return new ClassroomResource('Classroom created successfully', $classroom, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        } catch (ValidationException $e) {
            return response()->json('Validation error: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $classroom = Classroom::with('parent')->findOrFail($id);
            return new ClassroomResource('Success', $classroom, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|number|max:255',
                'description' => 'nullable|number|max:255',
            ]);

            $classroom = Classroom::findOrFail($id);
            $classroom->update($validated);

            return new ClassroomResource('Classroom updated successfully', $classroom, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        } catch (ValidationException $e) {
            return response()->json('Validation error: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $classroom = Classroom::findOrFail($id);
            $classroom->delete();

            return new ClassroomResource('Classroom deleted successfully', null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json('No data found', 404);
        } catch (\Throwable $th) {
            return response()->json('An error occurred: ' . $th->getMessage(), 500);
        }
    }
}
