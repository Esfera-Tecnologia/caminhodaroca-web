<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatedPartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "logo" => $this->logo_url,
            "category" => $this->resource->category->name,
            "subcategory" => $this->resource->subcategory->name,
            "city" => $this->city,
            "state" => 'RJ'
        ];
    }
}
