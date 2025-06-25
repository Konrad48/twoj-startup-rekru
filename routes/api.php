<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;

// Public routes

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'destroy']);
    Route::post('/logout', [UserController::class, 'logout']);
    
    Route::post('/emails/send-welcome', [EmailController::class, 'sendWelcomeEmails']);
    Route::apiResource('emails', EmailController::class)->except([
        'update'
    ]);
});
