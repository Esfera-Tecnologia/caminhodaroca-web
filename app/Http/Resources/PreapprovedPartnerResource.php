<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreapprovedPartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->partner_id,
            'logo' => $this->logo_url,
            'name' => $this->name,
            'cities' => $this->resource->cities->pluck('name', 'id'),
            'uf' => 'RJ',
            'description' => $this->description,
            'email' => $this->email,
            'routes' => $this->routes,
            'circuits' => $this->circuits,
            'attractions' => $this->attractions,
            'instagram' => $this->instagram,
            'website' => $this->site,
            'site' => $this->site,
            'events' => PreapprovedEventResource::collection($this->resource->events),
        ];
    }
}
