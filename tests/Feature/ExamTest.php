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
        $response = $this->actingAs($admin, 'sanctum')->postJson("/exams/{$exam->id}/users/{$student->id}");

        // check status
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Exam successfully associated with user']);

        // check if there is the entry in db
        $this->assertDatabaseHas('users_exams', [
            'user_id' => $student->id,
            'exam_id' => $exam->id,
        ]);
    }

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

    public function test_admin_can_create_exam()
    {
        // create admin role if not exist
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        $user = User::factory()->create(['role_id' => $role->id]);

        // make request to create new exam
        $response = $this->actingAs($user, 'sanctum')->post('/exams', [
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

        $response = $this->actingAs($user, 'sanctum')->post('/exams', [
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

        $response = $this->actingAs($user, 'sanctum')->post("/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'vote' => 28]);
    }

    public function test_not_supervisor_cannot_assign_vote()
    {

        $user = User::factory()->create();

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_access_exams()
    {
        // create user
        $user = User::factory()->create();

        // make get request to your exams
        $response = $this->actingAs($user, 'sanctum')->get('/yours-exams');

        // check response
        $response->assertStatus(200);
    }

    public function test_view_all_exams()
    {
        // create exams
        $exam1 = Exam::factory()->create([
            'title' => 'Esame di Matematica',
            'date' => '2024-12-01',
        ]);

        $exam2 = Exam::factory()->create([
            'title' => 'Esame di Fisica',
            'date' => '2024-12-10',
        ]);

        $exam3 = Exam::factory()->create([
            'title' => 'Esame di Chimica',
            'date' => '2024-12-05',
        ]);

        // make request without filters
        $response = $this->getJson('/all-exams');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_filter_exams_by_title()
    {
        $exam1 = Exam::factory()->create([
            'title' => 'Esame di Matematica',
            'date' => '2024-12-01',
        ]);

        $exam2 = Exam::factory()->create([
            'title' => 'Esame di Fisica',
            'date' => '2024-12-10',
        ]);

        $exam3 = Exam::factory()->create([
            'title' => 'Esame di Chimica',
            'date' => '2024-12-05',
        ]);

        // make request with title filter
        $response = $this->getJson('/all-exams?title=Fisica');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']);
        $response->assertJsonCount(1);
    }

    public function test_filter_exams_by_date()
    {
        $exam1 = Exam::factory()->create([
            'title' => 'Esame di Matematica',
            'date' => '2024-12-01',
        ]);

        $exam2 = Exam::factory()->create([
            'title' => 'Esame di Fisica',
            'date' => '2024-12-10',
        ]);

        $exam3 = Exam::factory()->create([
            'title' => 'Esame di Chimica',
            'date' => '2024-12-05',
        ]);

        // make request with date filter
        $response = $this->getJson('/all-exams?date=2024-12-05');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Chimica']);
        $response->assertJsonCount(1);

    }

    public function test_filter_exams_sort_by_date_asc()
    {
        $exam1 = Exam::factory()->create([
            'title' => 'Esame di Matematica',
            'date' => '2024-12-01',
        ]);

        $exam2 = Exam::factory()->create([
            'title' => 'Esame di Fisica',
            'date' => '2024-12-10',
        ]);

        $exam3 = Exam::factory()->create([
            'title' => 'Esame di Chimica',
            'date' => '2024-12-05',
        ]);

        // make request sorting by date in ascendent mode
        $response = $this->getJson('/all-exams?sort=date');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Matematica']);
        $response->assertJsonFragment(['title' => 'Esame di Chimica']);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']);
    }

    public function test_filter_exams_sort_by_date_desc()
    {
        $exam1 = Exam::factory()->create([
            'title' => 'Esame di Matematica',
            'date' => '2024-12-01',
        ]);

        $exam2 = Exam::factory()->create([
            'title' => 'Esame di Fisica',
            'date' => '2024-12-10',
        ]);

        $exam3 = Exam::factory()->create([
            'title' => 'Esame di Chimica',
            'date' => '2024-12-05',
        ]);

        // make request sorting by date in descendent mode
        $response = $this->getJson('/all-exams?sort=-date');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']);
        $response->assertJsonFragment(['title' => 'Esame di Chimica']);
        $response->assertJsonFragment(['title' => 'Esame di Matematica']);
    }
}

