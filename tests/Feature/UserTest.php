<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
