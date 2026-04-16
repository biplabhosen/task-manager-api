<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
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

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved successfully.',
            'data' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        $task = $this->taskService->getTask($request->user(), $task->id);

        return response()->json([
            'success' => true,
            'message' => 'Task retrieved successfully.',
            'data' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateTask(
            $request->user(),
            $task->id,
            $request->validated(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->taskService->deleteTask($request->user(), $task->id);

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
            'data' => null,
        ]);
    }

}
