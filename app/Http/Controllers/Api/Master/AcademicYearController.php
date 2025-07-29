<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicYearResource;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = AcademicYear::all();
            return new AcademicYearResource('Data retrieved successfully', $data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve data',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|string|max:9|unique:academic_years,year',
                'semester' => 'required|string|max:20',
                'active' => 'boolean',
                'description' => 'nullable|string',
            ]);

            $academicYear = AcademicYear::create([
                'year' => $request->year,
                'semester' => $request->semester,
                'active' => $request->active,
                'description' => $request->description,
            ]);

            return new AcademicYearResource('Academic year created successfully', $academicYear, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);
            return new AcademicYearResource('Data retrieved successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'year' => 'required|string|max:9|unique:academic_years,year,' . $id,
                'semester' => 'required|string|max:20',
                'active' => 'boolean',
                'description' => 'nullable|string',
            ]);

            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->update([
                'year' => $request->year,
                'semester' => $request->semester,
                'active' => $request->active,
                'description' => $request->description,
            ]);

            return new AcademicYearResource('Academic year updated successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update academic year',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function setActive(Request $request, string $id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->active = $request->active;
            $academicYear->save();

            return new AcademicYearResource('Academic year status updated successfully', $academicYear, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update academic year status',
                'status' => 500,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
