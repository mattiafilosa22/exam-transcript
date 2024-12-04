<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials()
    {
        // create user
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // try to authenticate
        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // check response status
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_authenticated_user_can_access_exams()
    {
        // create user
        $user = User::factory()->create();

        // make get request to your exams
        $response = $this->actingAs($user, 'sanctum')->get('api/exams/user');

        // check response
        $response->assertStatus(200);
    }

    public function test_user_cannot_create_exam()
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/exams', [
            'title' => 'Science Exam',
            'date' => '2024-12-01',
            'vote' => 28
        ]);

        $response->assertStatus(403);
    }
}
