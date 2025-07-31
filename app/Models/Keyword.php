<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;
    protected $fillable = ['term'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_keyword');
    }
}
