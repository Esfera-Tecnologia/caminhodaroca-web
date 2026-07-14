<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerEventResource extends JsonResource 
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'eventId' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'externalLink' => $this->url ?: '',
            'images' => collect($this->resource->images)->pluck('image_url')->toArray(),
        ];
    }
}
