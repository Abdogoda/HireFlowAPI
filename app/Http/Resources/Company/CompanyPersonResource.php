<?php

namespace App\Http\Resources\Company;

use App\Enums\Company\CompanyRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserSimpleResource;

class CompanyPersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_role' => $this->company_role,
            'position' => $this->position,
            'information' => $this->information,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_current_position' => $this->is_current_position,
            'user' => new UserSimpleResource($this->whenLoaded('member')),
            'is_company_owner' => $this->company_role === CompanyRoles::OWNER->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
