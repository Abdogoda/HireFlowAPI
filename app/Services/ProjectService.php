<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Http\Resources\Profile\ProjectResource;
use Illuminate\Http\JsonResponse;

class ProjectService
{
    /**
     * Create a new project for user
     *
     * @param User $user
     * @param array $data
     * @return ProjectResource|JsonResponse
     */
    public function createProject(User $user, array $data): ProjectResource
    {
        $project = $user->projects()->create($data);
        return new ProjectResource($project);
    }

    /**
     * Update a project
     *
     * @param Project $project
     * @param array $data
     * @return ProjectResource|JsonResponse
     */
    public function updateProject(Project $project, array $data): ProjectResource
    {
        $project->update($data);
        return new ProjectResource($project);
    }

    /**
     * Delete a project
     *
     * @param Project $project
     * @return bool|JsonResponse
     */
    public function deleteProject(Project $project): bool
    {
        $project->delete();
        return true;
    }
}
