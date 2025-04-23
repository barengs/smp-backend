<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'user_id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'nik' => '1234567890',
            'address' => 'Jl. Admin No. 1',
            'email' => 'admin@gmail.com',
            'zip_code' => '12345',
            'phone' => '081234567890',
        ]);
    }
}
