<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Storage; 
use Intervention\Image\ImageManager as Image;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Book::with('categories')->latest()->get(); // Get latest books with their categories
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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id' // Each item in the array must be a valid category ID
            'book_file' => 'nullable|file|mimes:pdf,epub|max:20480', // 20MB limit for PDFs/ePubs
        ]);

        if ($request->hasFile('cover_image')) {
            $imageFile = $request->file('cover_image');

            // Intervention v3 image handling
            $image = Image::gd()->read($imageFile);

            $image->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $imageName = uniqid() . '.jpg';

            // Save as high-quality JPEG
            $image->toJpeg(75)->save(storage_path('app/temp/' . $imageName));

            // Move to final public folder
            $path = Storage::disk('public')->putFileAs(
                'covers',
                new \Illuminate\Http\File(storage_path('app/temp/' . $imageName)),
                $imageName
            );

            unlink(storage_path('app/temp/' . $imageName));

            $validatedData['cover_image_url'] = $path;
        }

        // --- NEW: Book File Handling ---
        if ($request->hasFile('book_file')) {
            // Store the file in 'storage/app/public/books'
            $filePath = $request->file('book_file')->store('books', 'public');
            $validatedData['file_url'] = $filePath;
        }

        $book = Book::create($validatedData);

        // After creating or updating the book, sync the categories
        if ($request->has('categories')) {
            $book->categories()->sync($request->categories);
        }

        return response()->json($book, 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->increment('view_count'); // Increment the view count each time this method is called
    
        return $book->load('categories'); // Load categories for a single book
    }

    public function latest()
    {
        // Get the 8 most recently created books, with their categories
        $latestBooks = Book::with('categories')->latest()->take(8)->get();
        return response()->json($latestBooks);
    }

    public function popular()
    {
        // Get the 8 most viewed books, with their categories
        $popularBooks = Book::with('categories')->orderBy('view_count', 'desc')->take(8)->get();
        return response()->json($popularBooks);
    }

    public function search(Request $request)
    {
        // Validate that a search query 'q' was provided
        $query = $request->validate(['q' => 'required|string|min:3']);

        // Search the books table
        $results = Book::with('categories')
            ->where('title', 'like', "%{$query['q']}%")
            ->orWhere('author', 'like', "%{$query['q']}%")
            ->take(10)
            ->get();
        
        return response()->json($results);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'publisher' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB limit
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id' // Each item in the array must be a valid category ID
            'book_file' => 'nullable|file|mimes:pdf,epub|max:20480',
        ]);

        // --- Intelligent Image Handling ---
        if ($request->hasFile('cover_image')) {
            // 1. A new image was uploaded. First, delete the old one.
            if ($book->cover_image_url) {
                Storage::disk('public')->delete($book->cover_image_url);
            }

            // 2. Process and store the new image (same logic as our `store` method)
            $imageFile = $request->file('cover_image');
            $image = Image::gd()->read($imageFile);
            $image->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $imageName = uniqid() . '.jpg';
            $image->save(storage_path('app/temp/' . $imageName));
            $path = Storage::disk('public')->putFileAs('covers', new \Illuminate\Http\File(storage_path('app/temp/' . $imageName)), $imageName);
            unlink(storage_path('app/temp/' . $imageName));

            // 3. Add the new image path to our data for updating.
            $validatedData['cover_image_url'] = $path;
        }

        // --- NEW: Book File Handling ---
        if ($request->hasFile('book_file')) {
            // Delete the old file if it exists
            if ($book->file_url) {
                Storage::disk('public')->delete($book->file_url);
            }
            // Store the new file
            $filePath = $request->file('book_file')->store('books', 'public');
            $validatedData['file_url'] = $filePath;
        }
    

        // --- Update the Book record ---
        $book->update($validatedData);

        // After creating or updating the book, sync the categories
        if ($request->has('categories')) {
            $book->categories()->sync($request->categories);
        }

        // Return the newly updated book data.
        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // 1. Check if there is an associated image file.
        if ($book->cover_image_url) {
            // 2. Use the Storage facade to delete the file from the public disk.
            // This physically removes the image from the `storage/app/public` folder.
            Storage::disk('public')->delete($book->cover_image_url);
        }

        // 3. Now, delete the book record from the database.
        $book->delete();

        // 4. Return a success response.
        return response()->noContent();
    }
}
