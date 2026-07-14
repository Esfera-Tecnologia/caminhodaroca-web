<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'full_description' => $this->full_description,
            'organization' => !empty($this->organization) ? $this->organization : '',
            'start_date' => $this->start_date?->toIso8601String(),
            'end_date' => $this->end_date?->toIso8601String(),
            'image_url' => $this->image_url,
            'url' => $this->url ?: '',
            'location' => ($this->city?->name ?? '') . ', ' . ($this->state?->code ?: $this->state?->name),
            'is_highlight' => (bool) $this->is_highlight,
            'expired' => $this->end_date ? $this->end_date->isPast() : false,
            'properties' => $this->whenLoaded('properties', function () {
                return optional($this->properties)->map(function ($prop) {
                    return [
                        'id' => $prop->id,
                        'name' => $prop->name
                    ];
                }) ?? [];
            }, []),
        ];
    }
}
