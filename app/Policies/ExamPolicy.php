<?php

// app/Policies/ExamPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * check if user can create an exam
     *
     * @param  \App\Models\User  $user
     *
     * @return bool
     */
    public function createExam(User $user): bool
    {
        // only admin user
        return $user->role?->name === 'admin';
    }

    /**
     * check if user can assign vote
     *
     * @param  \App\Models\User  $user
     *
     * @return bool
     */
    public function assignVote(User $user): bool
    {
        // only supervisor
        return $user->role?->name === 'supervisor';
    }

    /**
     * check if user can associate exam to user
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function associateExamToUser(User $user): bool
    {
        // only admin
        return $user->role?->name === 'admin';
    }
}

