<?php

namespace App\Services;

use App\Models\User;
use App\Models\Skill;
use App\Http\Resources\Profile\SkillResource;
use Illuminate\Http\JsonResponse;

class SkillService
{
    /**
     * Create a new skill for user
     *
     * @param User $user
     * @param array $data
     * @return SkillResource|JsonResponse
     */
    public function createSkill(User $user, array $data): SkillResource
    {
        $skill = $user->skills()->create($data);
        return new SkillResource($skill);
    }

    /**
     * Update a skill
     *
     * @param Skill $skill
     * @param array $data
     * @return SkillResource|JsonResponse
     */
    public function updateSkill(Skill $skill, array $data): SkillResource
    {
        $skill->update($data);
        return new SkillResource($skill);
    }

    /**
     * Delete a skill
     *
     * @param Skill $skill
     * @return bool|JsonResponse
     */
    public function deleteSkill(Skill $skill): bool
    {
        $skill->delete();
        return true;
    }

    /**
     * Get all skills for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection|JsonResponse
     */
    public function getUserSkills(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->skills()->get();
    }
}