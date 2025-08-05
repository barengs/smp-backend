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
     * Menampilkan daftar semua santri
     *
     * Method ini digunakan untuk mengambil semua data santri dari database
     * beserta relasi program studi dan data orang tua.
     *
     * @group Students
     * @authenticated
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "student_id": "STU001",
     *       "name": "Ahmad Santri",
     *       "status": "Aktif",
     *       "program": {
     *         "id": 1,
     *         "name": "Program Tahfidz"
     *       },
     *       "parents": [
     *         {
     *           "id": 1,
     *           "name": "Bapak Ahmad",
     *           "relationship": "Ayah"
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "status": "error",
     *   "message": "No students found"
     * }
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
     * Menyimpan data santri baru
     *
     * Method ini digunakan untuk membuat data santri baru dengan validasi input
     * yang ketat. Data santri akan disimpan beserta relasi dengan program studi
     * dan data orang tua.
     *
     * @group Students
     * @authenticated
     *
     * @bodyParam student_id string required ID unik santri. Example: STU001
     * @bodyParam name string required Nama lengkap santri. Example: Ahmad Santri
     * @bodyParam program_id integer required ID program studi. Example: 1
     * @bodyParam parent_id integer required ID orang tua. Example: 1
     * @bodyParam status string Status santri (Aktif, Tugas, Alumni). Example: Aktif
     *
     * @response 201 {
     *   "message": "Student created successfully",
     *   "status": 201,
     *   "data": {
     *     "id": 1,
     *     "student_id": "STU001",
     *     "name": "Ahmad Santri",
     *     "status": "Aktif"
     *   }
     * }
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Menampilkan detail santri berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail santri spesifik berdasarkan ID
     * beserta relasi program studi dan data orang tua.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam id integer required ID santri yang akan ditampilkan. Example: 1
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "student_id": "STU001",
     *     "name": "Ahmad Santri",
     *     "status": "Aktif",
     *     "program": {
     *       "id": 1,
     *       "name": "Program Tahfidz"
     *     },
     *     "parents": [
     *       {
     *         "id": 1,
     *         "name": "Bapak Ahmad",
     *         "relationship": "Ayah"
     *       }
     *     ]
     *   }
     * }
     *
     * @response 404 {
     *   "status": "error",
     *   "message": "Student not found"
     * }
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
     * Mengupdate data santri yang ada
     *
     * Method ini digunakan untuk mengubah data santri yang sudah ada
     * dengan validasi input yang ketat.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam id integer required ID santri yang akan diupdate. Example: 1
     * @bodyParam name string Nama lengkap santri. Example: Ahmad Santri Updated
     * @bodyParam program_id integer ID program studi. Example: 1
     * @bodyParam parent_id integer ID orang tua. Example: 1
     * @bodyParam status string Status santri (Aktif, Tugas, Alumni). Example: Aktif
     *
     * @response 200 {
     *   "message": "Student updated successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "student_id": "STU001",
     *     "name": "Ahmad Santri Updated",
     *     "status": "Aktif"
     *   }
     * }
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Menghapus data santri
     *
     * Method ini digunakan untuk menghapus data santri berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan data santri harus dilakukan dengan hati-hati
     * karena dapat mempengaruhi data terkait.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam id integer required ID santri yang akan dihapus. Example: 1
     *
     * @response 200 {
     *   "message": "Student deleted successfully",
     *   "status": 200
     * }
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Mengambil santri berdasarkan ID orang tua
     *
     * Method ini digunakan untuk menampilkan semua santri yang terkait dengan
     * orang tua tertentu berdasarkan ID orang tua.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam parentId integer required ID orang tua. Example: 1
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "student_id": "STU001",
     *       "name": "Ahmad Santri",
     *       "status": "Aktif"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "status": "error",
     *   "message": "No students found for this parent"
     * }
     */
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

    /**
     * Mengambil santri berdasarkan ID program studi
     *
     * Method ini digunakan untuk menampilkan semua santri yang terdaftar dalam
     * program studi tertentu berdasarkan ID program.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam programId integer required ID program studi. Example: 1
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "student_id": "STU001",
     *       "name": "Ahmad Santri",
     *       "status": "Aktif",
     *       "program": {
     *         "id": 1,
     *         "name": "Program Tahfidz"
     *       }
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "status": "error",
     *   "message": "No students found for this program"
     * }
     */
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

    /**
     * Mengupdate status santri
     *
     * Method ini digunakan untuk mengubah status santri (Aktif, Tugas, Alumni)
     * berdasarkan ID santri.
     *
     * @group Students
     * @authenticated
     *
     * @urlParam id integer required ID santri yang statusnya akan diubah. Example: 1
     * @bodyParam status boolean required Status santri (true = Aktif, false = Nonaktif). Example: true
     *
     * @response 200 {
     *   "message": "Student status updated successfully",
     *   "data": {
     *     "id": 1,
     *     "student_id": "STU001",
     *     "name": "Ahmad Santri",
     *     "status": "Aktif"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Student not found"
     * }
     */
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
