<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

/*Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from the E-Library API!']);
});*/
Route::get('/books', [BookController::class, 'index']);// Route to get all books