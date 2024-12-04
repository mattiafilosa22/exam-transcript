<?php

// app/Policies/ExamPolicy.php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * check if user can create an exam
     */
    public function createExam(User $user): bool
    {
        // only admin user
        return $user->role?->name === 'admin';
    }

    /**
     * check if user can assign vote
     */
    public function assignVote(User $user): bool
    {
        // only supervisor
        return $user->role?->name === 'supervisor';
    }

    /**
     * check if user can associate exam to user
     */
    public function associateExamToUser(User $user): bool
    {
        // only admin
        return $user->role?->name === 'admin';
    }
}
