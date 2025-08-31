<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\User;
use App\Models\Staff;
use App\Imports\StaffImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use App\Http\Resources\StaffResource;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = User::whereHas('staff')->with(['staff', 'roles'])->get();
            return new StaffResource('data ditemukan', $data, 200);
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
            'nik' => 'required|unique:staff,nik',
            'address' => 'required',
            'phone' => 'required|numeric|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // run transaction
        DB::beginTransaction();

        try {
            // check last staff code
            $lastStaff = Staff::orderBy('created_at', 'desc')->first();
            $lastCode = $lastStaff ? $lastStaff->code : null;
            $staffCode = $this->generateCode('AS', $lastCode, 4);

            // create user
            $user = User::create([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // create staff
            $photoName = null;
            if ($request->hasFile('photo')) {
                $photoName = $this->uploadPhoto($request->file('photo'));
            }

            $user->staff()->create([
                'code' => $staffCode,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'photo' => $photoName,
            ]);

            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // commit transaction
            DB::commit();

            return new StaffResource('data berhasil disimpan', $user->load(['staff', 'roles']), 201);
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
            $data = User::whereHas('staff')->with(['staff', 'roles'])->findOrFail($id);
            return new StaffResource('data ditemukan', $data, 200);
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
            'nik' => 'required|unique:staff,nik,' . $id,
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

            // upload new photo if provided
            $photoName = $user->staff->photo;
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->staff->photo) {
                    Storage::disk('public')->delete('uploads/staff/' . $user->staff->photo);
                }
                $photoName = $this->uploadPhoto($request->file('photo'));
            }

            // update staff
            $user->staff()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
                'photo' => $photoName,
            ]);

            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // commit transaction
            DB::commit();

            return new StaffResource('data berhasil diubah', $user->load(['staff', 'roles']), 200);
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
        try {
            $user = User::findOrFail($id);

            // Delete photo if exists
            if ($user->staff->photo) {
                Storage::disk('public')->delete('uploads/staff/' . $user->staff->photo);
            }

            // Delete staff record
            $user->staff()->delete();

            // Delete user record
            $user->delete();

            return response()->json('data berhasil dihapus', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage using method spoofing.
     */
    public function updateWithSpoofing(Request $request, string $id)
    {
        // Validate that the request is intended to be a PUT/PATCH request
        if ($request->input('_method') !== 'PUT' && $request->input('_method') !== 'PATCH') {
            return response()->json(['message' => 'Invalid method. Use PUT or PATCH.'], 405);
        }

        // Call the existing update method
        return $this->update($request, $id);
    }

    /**
     * Update staff photo only.
     */
    public function updatePhoto(Request $request, string $id)
    {
        // Validate request
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Find user
            $user = User::findOrFail($id);

            // Upload new photo
            $newPhotoName = $this->uploadPhoto($request->file('photo'));

            // Delete old photo if exists
            if ($user->staff->photo) {
                Storage::disk('public')->delete('uploads/staff/' . $user->staff->photo);
            }

            // Update staff photo
            $user->staff()->update([
                'photo' => $newPhotoName,
            ]);

            return new StaffResource('Foto berhasil diubah', $user->load(['staff', 'roles']), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload and crop photo
     */
    private function uploadPhoto($photo)
    {
        $timestamp = now()->timestamp;
        $fileName = $timestamp . '_' . $photo->getClientOriginalName();

        // Create new image instance
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($photo->getRealPath());

        // Resize and crop the image to 151x227 pixels (approximately 4x6 cm at 96 DPI)
        $image->cover(151, 227); // This will crop and resize to exactly 151x227

        // Create directory if not exists
        if (!Storage::disk('public')->exists('uploads/staff')) {
            Storage::disk('public')->makeDirectory('uploads/staff');
        }

        // Save the image to the storage
        Storage::disk('public')->put('uploads/staff/' . $fileName, (string) $image->encode());

        return $fileName;
    }

    /**
     * Generate staff code
     */
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

    /**
     * Display a listing of teachers and class advisors.
     *
     * @group Staff
     * @authenticated
     *
     * @response 200 {
     *   "message": "data ditemukan",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Ahmad Guru",
     *       "email": "ahmad@example.com",
     *       "staff": {
     *         "id": 1,
     *         "code": "AS0001",
     *         "first_name": "Ahmad",
     *         "last_name": "Guru",
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
    public function getTeachersAndAdvisors()
    {
        try {
            $data = User::whereHas('staff')
                ->role(['asatidz', 'walikelas'])
                ->with(['staff', 'roles'])
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'data tidak ditemukan'], 404);
            }

            return new StaffResource('data ditemukan', $data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export staff data to Excel/CSV format
     */
    public function export(Request $request)
    {
        try {
            // Get all staff with their user and role data
            $staffs = User::whereHas('staff')
                ->with(['staff', 'roles'])
                ->get();

            // Prepare headers for CSV
            $headers = [
                'ID',
                'Kode Staff',
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
            foreach ($staffs as $staff) {
                $roles = $staff->roles->pluck('name')->implode(', ');

                $data[] = [
                    $staff->id,
                    $staff->staff->code ?? '',
                    $staff->staff->first_name . ' ' . ($staff->staff->last_name ?? ''),
                    $staff->staff->nik ?? '',
                    $staff->email,
                    $staff->staff->address ?? '',
                    $staff->staff->phone ?? '',
                    $staff->staff->zip_code ?? '',
                    $roles,
                    $staff->created_at->format('Y-m-d H:i:s'),
                    $staff->updated_at->format('Y-m-d H:i:s')
                ];
            }

            // Generate filename with timestamp
            $filename = 'staff_data_' . date('Y-m-d_H-i-s') . '.csv';

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

    /**
     * Import staff data from Excel/CSV file
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            $file = $request->file('file');
            $import = new StaffImport();
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
                ['Kode Staff', 'Nama Depan', 'Nama Belakang', 'NIK', 'Email', 'Alamat', 'Telepon', 'Kode Pos', 'Password', 'Role'],
                ['AS20240001', 'John', 'Doe', '1234567890123456', 'john.doe@example.com', 'Jl. Contoh No. 123', '081234567890', '12345', 'password123', 'staff'],
                ['AS20240002', 'Jane', 'Smith', '1234567890123457', 'jane.smith@example.com', 'Jl. Sample No. 456', '081234567891', '12346', 'password123', 'admin,staff'],
            ];

            // Generate filename
            $filename = 'staff_import_template_' . date('Y-m-d_H-i-s') . '.csv';

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
}
