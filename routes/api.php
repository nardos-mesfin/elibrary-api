<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthController; 

/*Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from the E-Library API!']);
});*/
Route::get('/books', [BookController::class, 'index']);// Route to get all books

// Public routes that do not require authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']);

// Protected routes that require authentication via Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/books', [BookController::class, 'store'])->middleware('isAdmin');
});