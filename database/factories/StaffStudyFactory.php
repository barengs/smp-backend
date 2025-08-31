<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\Study;
use App\Models\StaffStudy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffStudy>
 */
class StaffStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'study_id' => Study::factory(),
        ];
    }
}
