<?php

namespace App\Services;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillService
{
    /**
     * Create a new skill for user
     *
     * @param User $user
     * @param array $data
     * @return Skill|JsonResponse
     */
    public function createSkill(User $user, array $data): Skill|JsonResponse
    {
        try {
            $skill = $user->skills()->create($data);
            return $skill;
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
     * @return Skill|JsonResponse
     */
    public function updateSkill(Skill $skill, array $data): Skill|JsonResponse
    {
        try {
            $skill->update($data);
            return $skill;
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
