<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Profile\SkillResource;
use App\Http\Resources\Profile\ExperienceResource;
use App\Http\Resources\Profile\ProjectResource;
use App\Http\Resources\Profile\ResumeResource;
use App\Http\Resources\Profile\SocialProfileResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Full profile with all relationships
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
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'religion' => $this->religion,
            'email_verified_at' => $this->email_verified_at,
            'role' => new RoleResource($this->whenLoaded('role')),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'experiences' => ExperienceResource::collection($this->whenLoaded('experiences')),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'resumes' => ResumeResource::collection($this->whenLoaded('resumes')),
            'social_profiles' => SocialProfileResource::collection($this->whenLoaded('socialProfiles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
