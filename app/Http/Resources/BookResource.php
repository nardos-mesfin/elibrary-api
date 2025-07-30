<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'summary' => $this->summary,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'cover_image_url' => $this->cover_image_url,
            'full_cover_url' => $this->full_cover_url, // Include the accessor!
        ];
    }
}
