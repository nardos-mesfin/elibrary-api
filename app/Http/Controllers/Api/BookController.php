<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Storage; 

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all books from the database
        $books = Book::all(); 

        // Return the list of books as a JSON response
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'publisher' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validation for the image
        ]);

        if ($request->hasFile('cover_image')) {
            // Store the image in 'storage/app/public/covers'
            // The store method returns the path to the file.
            $path = $request->file('cover_image')->store('covers', 'public');
            $validatedData['cover_image_url'] = $path;
        }

        $book = Book::create($validatedData);

        return response()->json($book, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // Laravel's Route Model Binding automatically finds the book by its ID.
        // We can just return it. It will be automatically converted to JSON.
        return $book;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // The admin middleware has already verified the user.
        // We can safely delete the book.
        $book->delete();

        // Return a success response with no content.
        return response()->noContent();
    }
}
