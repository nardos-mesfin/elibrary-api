<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController; // Import with an alias

//======================================
// Public Routes
//======================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']); // Get all books is public
Route::get('/books/{book}', [BookController::class, 'show']);

//======================================
// Protected Routes (requires authentication)
//======================================
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

     // Admin-only routes
     Route::middleware('isAdmin')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
        Route::post('/books/{book}/update', [BookController::class, 'update']); 
        Route::get('/admin/users', [AdminUserController::class, 'index']);
    });
});