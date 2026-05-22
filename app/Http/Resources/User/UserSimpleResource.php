<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Role\RoleResource;

class UserSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Used for authentication responses (login/register)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'email_verified_at' => $this->email_verified_at,
            'role' => new RoleResource($this->whenLoaded('role')),
            'created_at' => $this->created_at,
        ];
    }
}
