<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\LessonHour;

class LessonHourTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing lesson hours.
     */
    public function test_can_list_lesson_hours()
    {
        LessonHour::factory()->count(3)->create();

        $response = $this->get('/api/lesson-hour');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success'
        ]);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test creating a lesson hour.
     */
    public function test_can_create_lesson_hour()
    {
        $data = [
            'name' => 'Jam Pelajaran 1',
            'start_time' => '07:00:00',
            'end_time' => '07:40:00',
            'order' => 1,
            'description' => 'Jam pelajaran pertama pagi',
        ];

        $response = $this->post('/api/lesson-hour', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Lesson hour created successfully'
        ]);

        $this->assertDatabaseHas('lesson_hours', $data);
    }

    /**
     * Test showing a specific lesson hour.
     */
    public function test_can_show_lesson_hour()
    {
        $lessonHour = LessonHour::factory()->create();

        $response = $this->get("/api/lesson-hour/{$lessonHour->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $lessonHour->id
            ]
        ]);
    }

    /**
     * Test updating a lesson hour.
     */
    public function test_can_update_lesson_hour()
    {
        $lessonHour = LessonHour::factory()->create();

        $updatedData = [
            'name' => 'Jam Pelajaran Updated',
            'start_time' => '08:00:00',
            'end_time' => '08:40:00',
            'order' => 2,
            'description' => 'Jam pelajaran yang diperbarui',
        ];

        $response = $this->put("/api/lesson-hour/{$lessonHour->id}", $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Lesson hour updated successfully'
        ]);

        $this->assertDatabaseHas('lesson_hours', $updatedData);
    }

    /**
     * Test deleting a lesson hour.
     */
    public function test_can_delete_lesson_hour()
    {
        $lessonHour = LessonHour::factory()->create();

        $response = $this->delete("/api/lesson-hour/{$lessonHour->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Lesson hour deleted successfully'
        ]);

        $this->assertDatabaseMissing('lesson_hours', [
            'id' => $lessonHour->id
        ]);
    }
}
