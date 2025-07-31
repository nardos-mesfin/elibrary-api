<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // <-- Import Storage

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'summary', 'publisher', 'pages', 
        'cover_image_url', 
        'file_url',
    ];

    protected $appends = ['full_cover_url', 'full_file_url'];

    /**
     * Get the full URL for the book's cover image.
     */
    public function getFullCoverUrlAttribute(): ?string
    {
        if ($this->cover_image_url) {
            return config('app.url') . Storage::url($this->cover_image_url);
        }
        return null;
    }

    /**
     * Get the full URL for the book's digital file.
     * THIS IS THE CORRECTLY NAMED METHOD.
     */
    public function getFullFileUrlAttribute(): ?string
    {
        if ($this->file_url) {
            return config('app.url') . Storage::url($this->file_url);
        }
        return null;
    }

    /**
     * The categories that belong to the book.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    // A book can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest(); // Show latest comments first
    }
    
}