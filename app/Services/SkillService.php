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
    public function createSkill(User $user, array $data): SkillResource|JsonResponse
    {
        try {
            $skill = $user->skills()->create($data);
            return new SkillResource($skill);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create skill',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update a skill
     *
     * @param Skill $skill
     * @param array $data
     * @return SkillResource|JsonResponse
     */
    public function updateSkill(Skill $skill, array $data): SkillResource|JsonResponse
    {
        try {
            $skill->update($data);
            return new SkillResource($skill);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update skill',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete a skill
     *
     * @param Skill $skill
     * @return bool|JsonResponse
     */
    public function deleteSkill(Skill $skill): bool|JsonResponse
    {
        try {
            $skill->delete();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete skill',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get all skills for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection|JsonResponse
     */
    public function getUserSkills(User $user): \Illuminate\Database\Eloquent\Collection|JsonResponse
    {
        try {
            return $user->skills()->get();
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve skills',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}