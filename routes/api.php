<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/exams', [ExamController::class, 'createExam'])->middleware('can:createExam,App\Models\Exam');
    Route::post('/exams/{exam}/users/{user}', [ExamController::class, 'associateExamToUser'])->middleware('can:associateExamToUser,App\Models\Exam');
    Route::post('/exams/{exam}/vote', [ExamController::class, 'assignVote'])->middleware('can:assignVote,exam');
    Route::get('/yours-exams', [ExamController::class, 'showUserExams']);
});

Route::get('/all-exams', [ExamController::class, 'getAll']);
