<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectService
{
    /**
     * Create a new project for user
     *
     * @param User $user
     * @param array $data
     * @return Project|JsonResponse
     */
    public function createProject(User $user, array $data): Project|JsonResponse
    {
        try {
            $project = $user->projects()->create($data);
            return $project;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create project',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update a project
     *
     * @param Project $project
     * @param array $data
     * @return Project|JsonResponse
     */
    public function updateProject(Project $project, array $data): Project|JsonResponse
    {
        try {
            $project->update($data);
            return $project;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update project',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete a project
     *
     * @param Project $project
     * @return bool|JsonResponse
     */
    public function deleteProject(Project $project): bool|JsonResponse
    {
        try {
            $project->delete();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete project',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
