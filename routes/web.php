<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:sanctum')->group(function () {
    // Rotta per la creazione di un esame (solo admin)
    Route::post('/exams', [ExamController::class, 'createExam'])->middleware('can:createExam,App\Models\Exam');
});

// Rotta per assegnare un voto a un esame (solo supervisor)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/exams/{exam}/vote', [ExamController::class, 'assignVote'])->middleware('can:assignVote,exam');
});

// Rotta per visualizzare gli esami dell'utente autenticato
Route::middleware('auth:sanctum')->get('/yours-exams', [ExamController::class, 'showUserExams']);

Route::get('/all-exams', [ExamController::class, 'getAll']);

Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        return response()->json(['token' => $user->createToken('ExamApp')->plainTextToken]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});
