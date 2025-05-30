<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\BookAuthorsController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\LoansController;
use App\Models\BookAuthors;

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('user', UsersController::class);
    Route::apiResource('author', AuthorsController::class);
    Route::apiResource('book', BooksController::class);
    Route::apiResource('loan', LoansController::class);
    Route::apiResource('book_author', BookAuthorsController::class);
   
});


