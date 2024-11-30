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
     * Determina se l'utente può creare un esame.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function createExam(User $user)
    {
        // Solo gli utenti con il ruolo "admin" possono creare un esame
        return $user->role?->name === 'admin';
    }

    /**
     * Determina se l'utente può assegnare un voto per un esame.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return bool
     */
    public function assignVote(User $user, Exam $exam)
    {
        // Solo gli utenti con il ruolo "supervisor" possono assegnare un voto
        return $user->role?->name === 'supervisor';
    }
}

