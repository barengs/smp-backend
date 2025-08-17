<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Http\Resources\InternshipResource;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InternshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $internships = Internship::with(['student', 'partner', 'supervisor'])
                ->orderBy('created_at', 'desc')
                ->get();
            return new InternshipResource('Internships fetched successfully', $internships, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch internships: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'partner_id' => 'required|exists:partners,id',
            'supervisor_id' => 'required|exists:internship_supervisors,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,terminated',
        ]);

        DB::beginTransaction();
        try {
            $internship = Internship::create($request->all());
            DB::commit();
            return new InternshipResource('Internship created successfully', $internship, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $internship = Internship::with(['student', 'partner', 'supervisor'])->findOrFail($id);
            return new InternshipResource('Data found', $internship, 200);
        } catch (\Throwable $th) {
            return response()->json('Data not found: ' . $th->getMessage(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'partner_id' => 'sometimes|required|exists:partners,id',
            'academic_year_id' => 'sometimes|required|exists:academic_years,id',
            'supervisor_id' => 'sometimes|required|exists:internship_supervisors,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:active,completed,terminated',
        ]);

        DB::beginTransaction();
        try {
            $internship = Internship::findOrFail($id);
            $internship->update($request->all());
            DB::commit();
            return new InternshipResource('Internship updated successfully', $internship, 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $internship = Internship::findOrFail($id);
            $internship->delete();
            DB::commit();
            return new InternshipResource('Internship deleted successfully', null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
