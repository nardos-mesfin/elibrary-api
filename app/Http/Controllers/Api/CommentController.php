<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class CommentController extends Controller
{
    // Get all comments for a specific book
    public function index(Book $book)
    {
        // Eager load the 'user' relationship to get the commenter's name
        return $book->comments()->with('user:id,name')->get();
    }

    // Store a new comment for a specific book
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate(['body' => 'required|string']);

        $comment = $book->comments()->create([
            'user_id' => auth()->id(), // Associate the comment with the currently logged-in user
            'body' => $validated['body'],
        ]);
        
        // Return the new comment, with the user info attached
        return $comment->load('user:id,name');
    }
}
