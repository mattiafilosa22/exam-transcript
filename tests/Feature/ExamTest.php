<?php

// In tests/Feature/ExamTest.php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_associate_exam()
    {
        // check if admin role exist if not create it
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        // create admin user
        $admin = User::factory()->create(['role_id' => $role->id]);

        // create student user
        $student = User::factory()->create();

        // create exam
        $exam = Exam::factory()->create();

        // call to associate exam to student by admin
        $response = $this->actingAs($admin, 'sanctum')->postJson("api/exams/{$exam->id}/users/{$student->id}");

        // check status
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Exam successfully associated with user']);

        // check if there is the entry in db
        $this->assertDatabaseHas('users_exams', [
            'user_id' => $student->id,
            'exam_id' => $exam->id,
        ]);
    }

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

