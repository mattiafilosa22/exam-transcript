<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SupervisorTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_assign_vote()
    {
        $role = Role::firstOrCreate(['id' => 2], ['name' => 'supervisor']);

        $user = User::factory()->create(['role_id' => $role->id]);

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("api/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'vote' => 28]);
    }

    public function test_not_supervisor_cannot_assign_vote()
    {

        $user = User::factory()->create();

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("api/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(403);
    }
}
