<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
