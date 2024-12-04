<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_vote_must_follow_check()
    {
        $role = Role::firstOrCreate(['id' => 2], ['name' => 'supervisor']);

        $user = User::factory()->create(['role_id' => $role->id]);

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("api/exams/{$exam->id}/vote", [
            'vote' => 35,
        ]);

        $response->assertStatus(302);
    }

    public function test_exam_title_is_mandatory()
    {
        // create admin role if not exist
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        $user = User::factory()->create(['role_id' => $role->id]);

        // make request to create new exam
        $response = $this->actingAs($user, 'sanctum')->post('api/exams', [
            'date' => '2024-12-10',
        ]);

        // check status of response
        $response->assertStatus(500);
    }

    public function test_exam_date_is_mandatory()
    {
        // create admin role if not exist
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        $user = User::factory()->create(['role_id' => $role->id]);

        // make request to create new exam
        $response = $this->actingAs($user, 'sanctum')->post('api/exams', [
            'title' => 'Analysis',
        ]);

        // check status of response
        $response->assertStatus(500);
    }
}
