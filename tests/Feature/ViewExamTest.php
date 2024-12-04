<?php

namespace Tests\Feature;

use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewExamTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->getJson('api/exams');
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
        $response = $this->getJson('api/exams?title=Fisica');

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
        $response = $this->getJson('api/exams?date=2024-12-05');

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
        $response = $this->getJson('api/exams?sort=date');
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
        $response = $this->getJson('api/exams?sort=-date');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Esame di Fisica']);
        $response->assertJsonFragment(['title' => 'Esame di Chimica']);
        $response->assertJsonFragment(['title' => 'Esame di Matematica']);
    }
}
