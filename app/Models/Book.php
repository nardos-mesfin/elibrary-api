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
        'cover_image_url', // <-- Add the cover image URL to the fillable array
    ];

    protected $appends = ['full_cover_url'];

    /**
     * Accessor for the full cover image URL.
     * This creates a virtual 'full_cover_url' attribute on the model.
     */
    public function getFullCoverUrlAttribute(): ?string
    {
        if ($this->cover_image_url) {
            // Return the full URL using the Storage facade
            return Storage::url($this->cover_image_url);
        }
        return null;
    }
    
}