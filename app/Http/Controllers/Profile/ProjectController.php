<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreProjectRequest;
use App\Http\Requests\Profile\UpdateProjectRequest;
use App\Http\Resources\Profile\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Get all projects for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $projects = $request->user()->projects()->get();

        return $this->successResponse(
            ['projects' => ProjectResource::collection($projects)],
            'Projects retrieved successfully'
        );
    }

    /**
     * Create a new project
     *
     * @param StoreProjectRequest $request
     * @param ProjectService $projectService
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request, ProjectService $projectService): JsonResponse
    {
        $user = $request->user();
        $result = $projectService->createProject($user, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['project' => $result],
            'Project created successfully'
        );
    }

    /**
     * Get a specific project
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function show(Request $request, Project $project): JsonResponse
    {
        if ($project->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to view this project');
        }

        return $this->successResponse(
            ['project' => new ProjectResource($project)],
            'Project retrieved successfully'
        );
    }

    /**
     * Update a project
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @param ProjectService $projectService
     * @return JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project, ProjectService $projectService): JsonResponse
    {
        if ($project->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to update this project');
        }

        $result = $projectService->updateProject($project, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['project' => $result],
            'Project updated successfully'
        );
    }

    /**
     * Delete a project
     *
     * @param Request $request
     * @param Project $project
     * @param ProjectService $projectService
     * @return JsonResponse
     */
    public function destroy(Request $request, Project $project, ProjectService $projectService): JsonResponse
    {
        if ($project->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to delete this project');
        }

        $result = $projectService->deleteProject($project);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'Project deleted successfully'
        );
    }
}
