<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\User;
use App\Models\Employee;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Imports\EmployeeImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar semua asatidz (karyawan)
     *
     * Method ini digunakan untuk mengambil semua data asatidz dari database
     * beserta relasi employee dan roles.
     *
     * @group Employees
     * @authenticated
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ahmad Asatidz",
     *       "email": "ahmad@example.com",
     *       "employee": {
     *         "id": 1,
     *         "code": "EMP0001",
     *         "first_name": "Ahmad",
     *         "last_name": "Asatidz",
     *         "nik": "1234567890123456",
     *         "phone": "081234567890"
     *       },
     *       "roles": [
     *         {
     *           "name": "asatidz"
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "message": "data tidak ditemukan"
     * }
     */
    public function index()
    {
        try {
            $data = User::whereHas('employee')->with(['employee', 'roles'])->get();
            return new EmployeeResource('data ditemukan', $data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nik' => 'required|unique:employees,nik',
            'address' => 'required',
            'phone' => 'required|numeric|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        // run transaction
        DB::beginTransaction();

        try {
            // check last employee code
            $lastEmployee = Employee::orderBy('created_at', 'desc')->first();
            $lastCode = $lastEmployee ? $lastEmployee->code : null;
            $employeeCode = $this->generateCode('EMP', $lastCode, 4);

            // create user
            $user = User::create([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // create employee
            $user->employee()->create([
                'code' => $employeeCode,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'photo' => $request->hasFile('photo') ? $this->uploadPhoto($request->file('photo')) : null,
            ]);
            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            // commit transaction
            DB::commit();

            return new EmployeeResource('data berhasil disimpan', $user->load(['employee', 'roles']), 201);
        } catch (ValidationException $e) {
            // pass the validation error to the response
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = User::whereHas('employee')->with(['employee', 'roles'])->findOrFail($id);
            return new EmployeeResource('data ditemukan', $data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validate request
        $validated = $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'nik' => 'required|unique:employees,nik,' . $id,
            'address' => 'required',
            'phone' => 'required|numeric|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        // run transaction
        DB::beginTransaction();

        try {
            // find user
            $user = User::findOrFail($id);
            // update user
            $user->update([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // update employee
            $user->employee()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'photo' => $request->hasFile('photo') ? $this->uploadPhoto($request->file('photo')) : $user->employee->photo,
            ]);
            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            // commit transaction
            DB::commit();

            return new EmployeeResource('data berhasil diubah', $user->load(['employee', 'roles']), 200);
        } catch (ValidationException $e) {
            // pass the validation error to the response
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Export employee data to Excel/CSV format
     */
    public function export(Request $request)
    {
        try {
            // Get all employees with their user and role data
            $employees = User::whereHas('employee')
                ->with(['employee', 'roles'])
                ->get();

            // Prepare headers for CSV
            $headers = [
                'ID',
                'Kode Pegawai',
                'Nama Lengkap',
                'NIK',
                'Email',
                'Alamat',
                'Telepon',
                'Kode Pos',
                'Role',
                'Tanggal Dibuat',
                'Tanggal Diperbarui'
            ];

            // Prepare data rows
            $data = [];
            foreach ($employees as $employee) {
                $roles = $employee->roles->pluck('name')->implode(', ');

                $data[] = [
                    $employee->id,
                    $employee->employee->code ?? '',
                    $employee->employee->first_name . ' ' . ($employee->employee->last_name ?? ''),
                    $employee->employee->nik ?? '',
                    $employee->email,
                    $employee->employee->address ?? '',
                    $employee->employee->phone ?? '',
                    $employee->employee->zip_code ?? '',
                    $roles,
                    $employee->created_at->format('Y-m-d H:i:s'),
                    $employee->updated_at->format('Y-m-d H:i:s')
                ];
            }

            // Generate filename with timestamp
            $filename = 'employee_data_' . date('Y-m-d_H-i-s') . '.csv';

            // Create CSV content
            $csvContent = $this->arrayToCsv($data, $headers);

            // Return CSV file as download
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan saat export: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Import employee data from Excel/CSV file
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            $file = $request->file('file');
            $import = new EmployeeImport();
            $import->import($file);

            $failures = $import->failures();
            $errors = [];
            if ($failures->isNotEmpty()) {
                foreach ($failures as $failure) {
                    $errors[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                }
                DB::rollBack();
                return response()->json([
                    'message' => 'Import gagal. Terdapat kesalahan pada beberapa baris.',
                    'success' => false,
                    'errors' => $errors,
                ], 422);
            }

            DB::commit();
            return response()->json([
                'message' => 'Import selesai.',
                'success' => true,
            ], 200);
        } catch (ExcelValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return response()->json([
                'message' => 'Terjadi kesalahan validasi saat import',
                'errors' => $errors,
                'success' => false,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }


    /**
     * Get import template
     */
    public function getImportTemplate()
    {
        try {
            // Create template data
            $templateData = [
                ['Kode Pegawai', 'Nama Depan', 'Nama Belakang', 'NIK', 'Email', 'Alamat', 'Telepon', 'Kode Pos', 'Password', 'Role'],
                ['EMP20240001', 'John', 'Doe', '1234567890123456', 'john.doe@example.com', 'Jl. Contoh No. 123', '081234567890', '12345', 'password123', 'employee'],
                ['EMP20240002', 'Jane', 'Smith', '1234567890123457', 'jane.smith@example.com', 'Jl. Sample No. 456', '081234567891', '12346', 'password123', 'admin,employee'],
            ];

            // Generate filename
            $filename = 'employee_import_template_' . date('Y-m-d_H-i-s') . '.csv';

            // Create CSV content
            $csvContent = $this->arrayToCsv($templateData, []);

            // Return CSV file as download
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json('Terjadi kesalahan saat membuat template: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Convert array to CSV format
     */
    private function arrayToCsv($data, $headers)
    {
        $output = fopen('php://temp', 'r+');

        // Add BOM for UTF-8 encoding
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function generateCode(string $prefix, ?string $last_code, int $padding = 4)
    {
        $currentYear = date('Y');
        $newSequence = 1;

        if ($last_code) {
            $lastYear = substr($last_code, strlen($prefix), 4);
            if ($lastYear == $currentYear) {
                $lastSequence = (int) substr($last_code, strlen($prefix) + 4);
                $newSequence = $lastSequence + 1;
            }
        }

        return $prefix . $currentYear . str_pad($newSequence, $padding, '0', STR_PAD_LEFT);
    }
    private function uploadPhoto($photo)
    {
        $timestamp = now()->timestamp;
        $fileName = $timestamp . '_' . $photo->getClientOriginalName();

        // Create new image instance
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($photo->getRealPath());

        // Resize and crop the image to 151x227 pixels (approximately 4x6 cm at 96 DPI)
        $image->cover(151, 227); // This will crop and resize to exactly 151x227

        // Save the image to the storage
        \Illuminate\Support\Facades\Storage::disk('public')->put('uploads/employees/' . $fileName, (string) $image->encode());

        return $fileName;
    }
}
