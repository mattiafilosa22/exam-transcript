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

    public function test_user_can_login_with_correct_credentials()
    {
        // Crea un utente per il test
        $user = User::factory()->create([
            'password' => Hash::make('password123') // Assicurati che la password sia corretta
        ]);

        // Fai una richiesta POST alla rotta di login con email e password corretti
        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password123', // Usa la password che hai creato per l'utente
        ]);

        // Verifica che la risposta contenga un token
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_admin_can_create_exam()
    {
        // Crea il ruolo admin se non esiste
        $role = Role::firstOrCreate(['id' => 1], ['name' => 'admin']);

        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'sanctum')->post('/exams', [
            'title' => 'Nuovo Esame',
            'date' => '2024-12-10',
        ]);

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

        $user = User::factory()->create(['role_id' => $role->id]);  // Supponiamo che l'ID 2 sia per 'supervisor'

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'vote' => 28]);
    }

    public function test_not_supervisor_cannot_assign_vote()
    {

        $user = User::factory()->create();  // Supponiamo che l'ID 2 sia per 'supervisor'

        $exam = Exam::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/exams/{$exam->id}/vote", [
            'vote' => 28,
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_access_exams()
    {
        // Crea un utente di test
        $user = User::factory()->create();

        // Autentica l'utente e fai una richiesta GET
        $response = $this->actingAs($user, 'sanctum')->get('/yours-exams');

        // Verifica la risposta
        $response->assertStatus(200);
    }

    public function test_view_all_exams()
    {
        // Crea esami con diverse date e titoli
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

        // Fai una richiesta GET senza filtri, controlla che tutti gli esami siano restituiti
        $response = $this->getJson('/all-exams');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_filter_exams_by_title()
    {
        // Crea esami con diverse date e titoli
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

        // Testa il filtro per titolo
        $response = $this->getJson('/all-exams?title=Fisica');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']); // Verifica che l'esame di Fisica sia presente
        $response->assertJsonCount(1); // Verifica che solo 1 esame sia restituito
    }

    public function test_filter_exams_by_date()
    {
        // Crea esami con diverse date e titoli
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

        // Testa il filtro per data
        $response = $this->getJson('/all-exams?date=2024-12-05');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Chimica']); // Verifica che l'esame di Chimica sia presente
        $response->assertJsonCount(1); // Verifica che solo 1 esame sia restituito

    }

    public function test_filter_exams_sort_by_date_asc()
    {
        // Crea esami con diverse date e titoli
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

        // Testa l'ordinamento per data (ascendente)
        $response = $this->getJson('/all-exams?sort=date');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Matematica']); // Verifica che l'esame di Matematica sia il primo
        $response->assertJsonFragment(['title' => 'Esame di Chimica']); // Verifica che l'esame di Chimica venga dopo
        $response->assertJsonFragment(['title' => 'Esame di Fisica']); // Verifica che l'esame di Fisica venga dopo ancora
    }

    public function test_filter_exams_sort_by_date_desc()
    {
        // Crea esami con diverse date e titoli
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

        // Testa l'ordinamento per data (discendente)
        $response = $this->getJson('/all-exams?sort=-date');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']); // Verifica che l'esame di Fisica sia il primo
        $response->assertJsonFragment(['title' => 'Esame di Chimica']); // Verifica che l'esame di Chimica venga dopo
        $response->assertJsonFragment(['title' => 'Esame di Matematica']); // Verifica che l'esame di Matematica venga dopo ancora
    }
}

