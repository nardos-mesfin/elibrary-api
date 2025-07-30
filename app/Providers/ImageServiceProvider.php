<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// We need to import the class we are trying to use
use Intervention\Image\ImageManager as Image;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // This is the magic. We are creating an "alias".
        // This tells Laravel: "From now on, whenever any part of the app sees the alias 'Image',
        // it should use the actual class 'Intervention\Image\ImageManagerStatic'."
        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Image', Image::class);
        });
    }
}