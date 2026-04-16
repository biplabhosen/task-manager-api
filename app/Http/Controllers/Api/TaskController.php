<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CompleteTaskRequest;
use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService,
    ) {
    }

    public function index(IndexTaskRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getUserTasks(
            $request->user(),
            $request->validated(),
        );

        return $this->successResponse(
            'Tasks retrieved successfully.',
            TaskResource::collection($tasks->items()),
            meta: [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        );
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask(
            $request->user(),
            $request->validated(),
        );

        return $this->successResponse(
            'Task created successfully.',
            new TaskResource($task),
            201,
        );
    }

    public function show(Request $request, int $task): JsonResponse
    {
        $task = $this->taskService->getTask($request->user(), $task);

        return $this->successResponse(
            'Task retrieved successfully.',
            new TaskResource($task),
        );
    }

    public function update(UpdateTaskRequest $request, int $task): JsonResponse
    {
        $task = $this->taskService->updateTask(
            $request->user(),
            $task,
            $request->validated(),
        );

        return $this->successResponse(
            'Task updated successfully.',
            new TaskResource($task),
        );
    }

    public function destroy(Request $request, int $task): JsonResponse
    {
        $this->taskService->deleteTask($request->user(), $task);

        return $this->successResponse('Task deleted successfully.');
    }

    public function complete(CompleteTaskRequest $request, int $task): JsonResponse
    {
        $task = $this->taskService->markTaskAsCompleted($request->user(), $task);

        return $this->successResponse(
            'Task marked as completed successfully.',
            new TaskResource($task),
        );
    }
}
