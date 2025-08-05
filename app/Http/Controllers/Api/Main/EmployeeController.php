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
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
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
        try {
            // Validate the uploaded file
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048', // 2MB max
            ]);

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Process the file based on its extension
            if ($extension === 'csv') {
                $data = $this->processCsvFile($file);
            } else {
                // For Excel files, we'll convert to CSV first
                $data = $this->processExcelFile($file);
            }

            if (empty($data)) {
                return response()->json([
                    'message' => 'File kosong atau format tidak sesuai',
                    'success' => false
                ], 400);
            }

            // Process the imported data
            $results = $this->processImportedData($data);

            return response()->json([
                'message' => 'Import berhasil',
                'success' => true,
                'data' => [
                    'total_rows' => count($data),
                    'success_count' => $results['success_count'],
                    'error_count' => $results['error_count'],
                    'errors' => $results['errors']
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Process CSV file
     */
    private function processCsvFile($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        // Skip header row
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 8) { // Minimum required columns
                $data[] = [
                    'code' => $row[0] ?? '',
                    'first_name' => $row[1] ?? '',
                    'last_name' => $row[2] ?? '',
                    'nik' => $row[3] ?? '',
                    'email' => $row[4] ?? '',
                    'address' => $row[5] ?? '',
                    'phone' => $row[6] ?? '',
                    'zip_code' => $row[7] ?? '',
                    'password' => $row[8] ?? 'password123', // Default password
                    'roles' => $row[9] ?? 'employee' // Default role
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    /**
     * Process Excel file (basic implementation)
     */
    private function processExcelFile($file)
    {
        // For now, we'll use CSV processing as fallback
        // In a real implementation, you'd use a library like PhpSpreadsheet
        return $this->processCsvFile($file);
    }

    /**
     * Process imported data and create employees
     */
    private function processImportedData($data)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                try {
                    // Validate required fields
                    if (empty($row['first_name']) || empty($row['email']) || empty($row['nik'])) {
                        $errors[] = "Baris " . ($index + 2) . ": Nama depan, email, dan NIK wajib diisi";
                        $errorCount++;
                        continue;
                    }

                    // Check if email already exists
                    if (User::where('email', $row['email'])->exists()) {
                        $errors[] = "Baris " . ($index + 2) . ": Email sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Check if NIK already exists
                    if (Employee::where('nik', $row['nik'])->exists()) {
                        $errors[] = "Baris " . ($index + 2) . ": NIK sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Generate employee code if not provided
                    if (empty($row['code'])) {
                        $lastEmployee = Employee::orderBy('created_at', 'desc')->first();
                        $lastCode = $lastEmployee ? $lastEmployee->code : null;
                        $row['code'] = $this->generateCode('EMP', $lastCode, 4);
                    }

                    // Create user
                    $user = User::create([
                        'name' => $row['first_name'],
                        'email' => $row['email'],
                        'password' => bcrypt($row['password']),
                    ]);

                    // Create employee
                    $user->employee()->create([
                        'code' => $row['code'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'] ?? '',
                        'nik' => $row['nik'],
                        'address' => $row['address'] ?? '',
                        'email' => $row['email'],
                        'password' => bcrypt($row['password']),
                        'phone' => $row['phone'] ?? '',
                        'zip_code' => $row['zip_code'] ?? '',
                    ]);

                    // Assign role
                    if (!empty($row['roles'])) {
                        $roles = explode(',', $row['roles']);
                        foreach ($roles as $role) {
                            $role = trim($role);
                            if (!empty($role)) {
                                $user->assignRole($role);
                            }
                        }
                    } else {
                        $user->assignRole('employee');
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $errorCount++;
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
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
            $lastYear = substr($last_code, 0, 4);
            if ($lastYear == $currentYear) {
                $lastSequence = (int) substr($last_code, strlen($prefix), -4);
                $newSequence = $lastSequence + 1;
            }
        }

        return $prefix . $currentYear . str_pad($newSequence, $padding, '0', STR_PAD_LEFT);
    }
}
