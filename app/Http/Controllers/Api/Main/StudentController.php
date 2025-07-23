<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch all students from the database
            $students = Student::with(['program', 'parents'])->get();

            return new StudentResource('data ditemukan', $students, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No students found',
            ], 404);
            //throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            //code...
            $student = Student::with(['program', 'parents'])->findOrFail($id);
            return new StudentResource('data ditemukan', $student, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student: ' . $th->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found',
            ], 404);
        }
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

    public function getStudentByParentId(string $parentId)
    {
        try {
            // Fetch students by parent ID
            $students = Student::where('parent_id', $parentId)->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No students found for this parent',
                ], 404);
            }

            return new StudentResource('data ditemukan', $students, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStudentByProgramId(string $programId)
    {
        try {
            // Fetch students by program ID
            $students = Student::where('program_id', $programId)->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No students found for this program',
                ], 404);
            }

            return new StudentResource('data ditemukan', $students, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function setStatus(Request $request, string $id)
    {
        try {
            $student = Student::findOrFail($id);

            $request->validate([
                'status' => 'required|boolean',
            ]);

            $student->status = $request->status;
            $student->save();

            return response()->json([
                'message' => 'Student status updated successfully',
                'data' => $student,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update student status',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
