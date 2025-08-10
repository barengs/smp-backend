<?php

namespace App\Imports;

use App\Http\Controllers\Api\Main\EmployeeController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $employeeController;

    public function __construct()
    {
        $this->employeeController = new EmployeeController();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['nama_depan'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
        ]);

        $employeeCode = $row['kode_pegawai'] ?? $this->generateEmployeeCode();

        $user->employee()->create([
            'code' => $employeeCode,
            'first_name' => $row['nama_depan'],
            'last_name' => $row['nama_belakang'],
            'nik' => $row['nik'],
            'address' => $row['alamat'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'phone' => $row['telepon'],
            'zip_code' => $row['kode_pos'],
        ]);

        if (!empty($row['role'])) {
            $roles = explode(',', $row['role']);
            $user->assignRole(array_map('trim', $roles));
        } else {
            $user->assignRole('employee');
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'nama_depan' => 'required',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|unique:employees,nik',
        ];
    }

    private function generateEmployeeCode()
    {
        $lastEmployee = Employee::orderBy('created_at', 'desc')->first();
        $lastCode = $lastEmployee ? $lastEmployee->code : null;
        return $this->employeeController->generateCode('EMP', $lastCode, 4);
    }
}
