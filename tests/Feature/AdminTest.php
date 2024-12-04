<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class AdminTest extends TestCase
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
        ]);

        // check status of response
        $response->assertStatus(201);
        $this->assertDatabaseHas('exams', ['title' => 'Nuovo Esame']);
    }

    public function test_not_admin_cannot_create_exam()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post('api/exams', [
            'title' => 'Nuovo Esame',
            'date' => '2024-12-10',
        ]);

        $response->assertStatus(403);
    }
}
