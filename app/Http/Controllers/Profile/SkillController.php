<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreSkillRequest;
use App\Http\Requests\Profile\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Services\SkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Get all skills for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request, SkillService $skillService): JsonResponse
    {
        $skills = $skillService->getUserSkills($request->user());

        return $this->successResponse(
            ['skills' => SkillResource::collection($skills)],
            'Skills retrieved successfully'
        );
    }

    /**
     * Create a new skill
     *
     * @param StoreSkillRequest $request
     * @param SkillService $skillService
     * @return JsonResponse
     */
    public function store(StoreSkillRequest $request, SkillService $skillService): JsonResponse
    {
        $user = $request->user();
        $result = $skillService->createSkill($user, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['skill' => $result],
            'Skill created successfully'
        );
    }

    /**
     * Get a specific skill
     *
     * @param Request $request
     * @param Skill $skill
     * @return JsonResponse
     */
    public function show(Request $request, Skill $skill): JsonResponse
    {
        if ($skill->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to view this skill');
        }

        return $this->successResponse(
            ['skill' => new SkillResource($skill)],
            'Skill retrieved successfully'
        );
    }

    /**
     * Update a skill
     *
     * @param UpdateSkillRequest $request
     * @param Skill $skill
     * @param SkillService $skillService
     * @return JsonResponse
     */
    public function update(UpdateSkillRequest $request, Skill $skill, SkillService $skillService): JsonResponse
    {
        if ($skill->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to update this skill');
        }

        $result = $skillService->updateSkill($skill, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['skill' => $result],
            'Skill updated successfully'
        );
    }

    /**
     * Delete a skill
     *
     * @param Request $request
     * @param Skill $skill
     * @param SkillService $skillService
     * @return JsonResponse
     */
    public function destroy(Request $request, Skill $skill, SkillService $skillService): JsonResponse
    {
        if ($skill->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to delete this skill');
        }

        $result = $skillService->deleteSkill($skill);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'Skill deleted successfully'
        );
    }
}
