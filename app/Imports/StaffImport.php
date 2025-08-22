<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class StaffImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // run transaction
        return DB::transaction(function () use ($row) {
            // check last staff code
            $lastStaff = Staff::orderBy('created_at', 'desc')->first();
            $lastCode = $lastStaff ? $lastStaff->code : null;
            $staffCode = $this->generateCode('AS', $lastCode, 4);

            // create user
            $user = User::create([
                'name' => $row['nama_depan'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
            ]);

            // create staff
            $staff = $user->staff()->create([
                'code' => $staffCode,
                'first_name' => $row['nama_depan'],
                'last_name' => $row['nama_belakang'] ?? null,
                'nik' => $row['nik'],
                'address' => $row['alamat'] ?? null,
                'email' => $row['email'],
                'phone' => $row['telepon'] ?? null,
                'zip_code' => $row['kode_pos'] ?? null,
            ]);

            // assign role if any roles are provided
            if (!empty($row['role'])) {
                $roles = explode(',', $row['role']);
                $roles = array_map('trim', $roles);
                $user->syncRoles($roles);
            }

            return $staff;
        });
    }

    public function rules(): array
    {
        return [
            'nama_depan' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|unique:staff,nik',
            'password' => 'required|min:6',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_depan.required' => 'Nama depan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle the error
        throw $e;
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failure
        throw new \Exception('Import failed: ' . implode(', ', array_map(function ($failure) {
            return 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }, $failures)));
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
}
