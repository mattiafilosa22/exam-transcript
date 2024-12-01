<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        return response()->json(['token' => $user->createToken('ExamApp')->plainTextToken]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});
