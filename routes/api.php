<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController; // Import with an alias
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\CommentController;

//======================================
// Public Routes
//======================================
// --- Public Routes ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/search', [BookController::class, 'search']);
Route::get('/books/{book}/comments', [CommentController::class, 'index']);

// --- Reordered Book Routes ---

// Most specific routes go first
Route::get('/books/latest', [BookController::class, 'latest']);
Route::get('/books/popular', [BookController::class, 'popular']);

Route::get('/categories/{category}/books', [BookController::class, 'booksByCategory']);

// More generic routes go after
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']); // The "greedy" route is now last

//======================================
// Protected Routes (requires authentication)
//======================================
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/books/{book}/comments', [CommentController::class, 'store']);

     // Admin-only routes
     Route::middleware('isAdmin')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
        Route::post('/books/{book}/update', [BookController::class, 'update']); 

        Route::get('/admin/users', [AdminUserController::class, 'index']);

        Route::get('/admin/categories', [CategoryController::class, 'index']);
        Route::post('/admin/categories', [CategoryController::class, 'store']);
    });
});