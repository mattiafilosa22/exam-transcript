<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/exams', [ExamController::class, 'createExam'])
        ->name('exams.store')
        ->middleware('can:createExam,App\Models\Exam');

    Route::post('/exams/{exam}/users/{user}', [ExamController::class, 'associateExamToUser'])
        ->name('exams.users.associate')
        ->middleware('can:associateExamToUser,App\Models\Exam');

    Route::post('/exams/{exam}/vote', [ExamController::class, 'assignVote'])
        ->name('exams.vote.assign')
        ->middleware('can:assignVote,exam');

    Route::get('/exams/user', [ExamController::class, 'showUserExams'])
        ->name('exams.users.index');
});

Route::get('/exams', [ExamController::class, 'getAll'])
    ->name('exams.index');

