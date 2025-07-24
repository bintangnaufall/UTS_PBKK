<?php

use App\Models\BookAuthors;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\BookAuthorsController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [OTPController::class, 'sendOTP']);
Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);
Route::post('/check-email', [AuthController::class, 'check']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('user', UsersController::class);
    Route::apiResource('author', AuthorsController::class);
    Route::apiResource('book', BooksController::class);
    Route::apiResource('loan', LoansController::class);
    Route::apiResource('book_author', BookAuthorsController::class);
    // Route::apiResource('book_author', BookAuthorsController::class);

    Route::get('user-count', [UsersController::class, 'usercount']);     
    Route::get('book-count', [BooksController::class, 'bookcount']);     
    Route::get('loan-count', [LoansController::class, 'loancount']);     
});


