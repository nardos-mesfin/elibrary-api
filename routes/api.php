<?php
use Illuminate\Support\Facades\Route;

Route::get('/greeting', function () {
    return response()->json(['message' => 'Hello from the E-Library API!']);
});