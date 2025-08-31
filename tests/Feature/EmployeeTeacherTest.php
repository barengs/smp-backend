<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Role;

class EmployeeTeacherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving teachers and advisors.
     *
     * @return void
     */
    public function test_can_retrieve_teachers_and_advisors()
    {
        // Create roles if they don't exist
        $teacherRole = Role::firstOrCreate(['name' => 'asatidz', 'guard_name' => 'api']);
        $advisorRole = Role::firstOrCreate(['name' => 'walikelas', 'guard_name' => 'api']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        // Create employees with different roles
        $teacherUser = User::factory()->create(['name' => 'Teacher User', 'email' => 'teacher@example.com']);
        $teacherEmployee = Employee::factory()->create(['user_id' => $teacherUser->id]);
        $teacherUser->assignRole($teacherRole);

        $advisorUser = User::factory()->create(['name' => 'Advisor User', 'email' => 'advisor@example.com']);
        $advisorEmployee = Employee::factory()->create(['user_id' => $advisorUser->id]);
        $advisorUser->assignRole($advisorRole);

        $adminUser = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com']);
        $adminEmployee = Employee::factory()->create(['user_id' => $adminUser->id]);
        $adminUser->assignRole($adminRole);

        // Make the request
        $response = $this->get('/api/employee/teachers-advisors');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'data ditemukan',
            'status' => 200,
        ]);

        // Assert that we only get teachers and advisors (2 users)
        $response->assertJsonCount(2, 'data');

        // Assert that the response contains the teacher and advisor
        $response->assertJsonFragment(['name' => 'Teacher User']);
        $response->assertJsonFragment(['name' => 'Advisor User']);

        // Assert that the response does not contain the admin
        $response->assertJsonMissing(['name' => 'Admin User']);
    }

    /**
     * Test retrieving teachers and advisors when none exist.
     *
     * @return void
     */
    public function test_returns_empty_array_when_no_teachers_or_advisors_exist()
    {
        // Create roles if they don't exist
        $teacherRole = Role::firstOrCreate(['name' => 'asatidz', 'guard_name' => 'api']);
        $advisorRole = Role::firstOrCreate(['name' => 'walikelas', 'guard_name' => 'api']);

        // Create a regular employee without teacher or advisor role
        $regularUser = User::factory()->create(['name' => 'Regular User', 'email' => 'regular@example.com']);
        $regularEmployee = Employee::factory()->create(['user_id' => $regularUser->id]);

        // Make the request
        $response = $this->get('/api/employee/teachers-advisors');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'data ditemukan',
            'status' => 200,
        ]);

        // Assert that we get an empty array
        $response->assertJsonCount(0, 'data');
    }
}
