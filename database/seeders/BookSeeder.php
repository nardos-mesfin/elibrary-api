<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the table first to avoid duplicates when re-seeding
        Book::truncate();

        Book::create([
            'title' => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
            'summary' => 'A great adventure in Middle-earth.',
            'publisher' => 'Allen & Unwin',
            'pages' => 1178,
            'is_verified' => true,
        ]);

        Book::create([
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'summary' => 'The story of Paul Atreides and the planet Arrakis.',
            'publisher' => 'Chilton Books',
            'pages' => 412,
            'is_verified' => true,
        ]);

        Book::create([
            'title' => 'A Brief History of Time',
            'author' => 'Stephen Hawking',
            'summary' => 'From the Big Bang to black holes.',
            'publisher' => 'Bantam Dell Publishing Group',
            'pages' => 256,
            'is_verified' => false,
        ]);
    }
}
