<?php

namespace Database\Seeders;

use App\Models\Education;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["name" => "SD", "description" => "Sekolah Dasar"],
            ["name" => "SMP", "description" => "Sekolah Menengah Pertama"],
            ["name" => "SMA", "description" => "Sekolah Menengah Atas"],
            ["name" => "SMK", "description" => "Sekolah Menengah Kejuruan"],
            ["name" => "MA", "description" => "Madrasah Aliyah"],
            ["name" => "MI", "description" => "Madrasah Ibtida'iyah"],
            ["name" => "MTs", "description" => "Madrasah Tsanawiyah"],
            ["name" => "MAK", "description" => "Madrasah Kejuruan"],
        ];

        foreach ($data as $value) {
            $edu = Education::create([
                'name' => $value['name'],
                'description' => $value['description'],
            ]);
            // $edu->education_class()->attach($edu->id);
            DB::table('education_has_education_classes')->insert([
                'education_id' => $edu->id,
                'education_class_id' => 1,
            ]);
        }
    }
}
