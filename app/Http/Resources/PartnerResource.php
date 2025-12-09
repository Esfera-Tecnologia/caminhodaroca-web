<?php

namespace App\Http\Resources;

use App\Enums\PreapprovedPartnerStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = request()->user();
        $userPartners = $user?->partner->pluck('id')->toArray()??[];
        $canEdit = in_array($this->id, $userPartners);
        
        if(isset($this->individual) && $this->individual) {
            return [
                'id' => $this->id,
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
                'events' => EventResource::collection($this->resource->events),
            ];
        }
        return [
            'id' => $this->id,
            'logo' => $this->logo_url,
            'name' => $this->name,
            'cities' => $this->resource->cities?->pluck('name', 'id'),
            'state' => 'Rio de Janeiro',
            'editable' => $canEdit,
            'pendingApproval' => $this->resource->preapproved_partner()->first()?->status == PreapprovedPartnerStatus::PENDING,
        ];
    }
}
