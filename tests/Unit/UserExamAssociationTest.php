<?php

// tests/Unit/UserExamAssociationTest.php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserExamAssociationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_multiple_exams()
    {
        $user = User::factory()->create();
        $exam1 = Exam::factory()->create();
        $exam2 = Exam::factory()->create();

        $user->exams()->attach([$exam1->id, $exam2->id]);

        $this->assertCount(2, $user->exams);
    }

    public function test_user_cannot_take_same_exam_multiple_times()
    {
        $user = User::factory()->create();
        $exam = Exam::factory()->create();

        $user->exams()->attach($exam->id);

        $this->expectException(\Exception::class);

        $user->exams()->attach($exam->id);
    }
}
