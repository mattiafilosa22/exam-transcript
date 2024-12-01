<?php

namespace Database\Seeders;

use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::create([
            'title' => 'Analisi 3',
            'date' => now(),
        ]);

        Exam::create([
            'title' => 'Analisi 2',
            'date' => Carbon::createFromFormat('Y-m-d', '2023-12-14'),
        ]);

        Exam::create([
            'title' => 'Analisi 1',
            'date' => Carbon::createFromFormat('Y-m-d', '2024-02-20'),
        ]);
    }
}
