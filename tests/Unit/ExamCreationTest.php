<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class ExamCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_exam()
    {
        // create admin role if not exist
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        $user = User::factory()->create(['role_id' => $role->id]);

        // make request to create new exam
        $response = $this->actingAs($user, 'sanctum')->post('api/exams', [
            'title' => 'Nuovo Esame',
            'date' => '2024-12-10',
            'vote' => 23,
        ]);

        // check status of response
        $response->assertStatus(201);
        $this->assertDatabaseHas('exams', ['title' => 'Nuovo Esame']);
    }

    public function test_user_cannot_create_exam()
    {

        $role = Role::firstOrCreate(['id' => 1], ['name' => 'user']);

        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user);

        $response = $this->postJson('/api/exams', [
            'title' => 'Science Exam',
            'date' => '2024-12-01',
            'vote' => 28
        ]);

        $response->assertStatus(403);
    }
}
