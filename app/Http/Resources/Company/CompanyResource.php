<?php

namespace App\Http\Resources\Company;

use App\Enums\Authorization\CompanyRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserSimpleResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'website' => $this->website,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'founded_year' => $this->founded_year,
            'location' => $this->location,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_by' => $this->created_by,
            'is_verified' => $this->is_verified,
            'owner' => $this->whenLoaded('owner') ? array_merge(
                (new UserSimpleResource($this->owner))->toArray($request),
                ['company_role' => CompanyRoles::OWNER->value]
            ) : null,
            'people' => CompanyPersonResource::collection($this->whenLoaded('memberships')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
