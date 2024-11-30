<?php

// database/factories/ExamFactory.php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'date' => $this->faker->date(),
            'vote' => null,
        ];
    }
}
