<?php

namespace App\Services;

use App\Models\User;
use App\Models\Skill;
use App\Http\Resources\SkillResource;
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
}