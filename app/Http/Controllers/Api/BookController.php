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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
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
    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'publisher' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB limit
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

        // --- Update the Book record ---
        $book->update($validatedData);

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
