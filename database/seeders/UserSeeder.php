<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    protected $password;
    protected $data;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->password = Hash::make('password');
        $this->data = [
            ["name" => "superadmin", "email" => "superadmin@mail.com", "password" => $this->password],
            ["name" => "erfaruq", "email" => "erfaruq@mail.com", "password" => $this->password],
            ["name" => "rumhul", "email" => "rumhul@mail.com", "password" => $this->password],
            ["name" => "rosi", "email" => "rosi@mail.com", "password" => $this->password],
            ["name" => "bahul", "email" => "bahul@mail.com", "password" => $this->password],
        ];

        foreach ($this->data as $value) {
            User::create($value);
        }
    }
}
