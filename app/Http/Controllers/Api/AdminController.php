<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        $users = User::query()->latest()->paginate(15);

        return $this->successResponse(
            'Users retrieved successfully.',
            UserResource::collection($users->items()),
            meta: [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        );
    }

    public function tasks(IndexTaskRequest $request): JsonResponse
    {
        $tasks = $this->taskQuery($request)
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            'Admin tasks retrieved successfully.',
            TaskResource::collection($tasks->items()),
            meta: [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        );
    }

    public function updateTask(UpdateTaskRequest $request, int $task): JsonResponse
    {
        $task = $this->findTask($task);
        $task->update($request->validated());

        return $this->successResponse(
            'Admin task updated successfully.',
            new TaskResource($task->fresh()->load('user:id,name,email,role')),
        );
    }

    public function destroyTask(int $task): JsonResponse
    {
        $this->findTask($task)->delete();

        return $this->successResponse('Admin task deleted successfully.');
    }

    public function updateUserRole(UpdateUserRoleRequest $request, int $user): JsonResponse
    {
        $user = User::query()->findOrFail($user);
        $user->update($request->validated());

        return $this->successResponse(
            'User role updated successfully.',
            new UserResource($user->fresh()),
        );
    }

    private function taskQuery(IndexTaskRequest $request)
    {
        return Task::query()
            ->select([
                'id',
                'user_id',
                'title',
                'description',
                'status',
                'due_date',
                'created_at',
                'updated_at',
            ])
            ->with('user:id,name,email,role')
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status')),
            )
            ->when(
                $request->filled('due_date'),
                fn ($query) => $query->whereDate('due_date', $request->date('due_date')),
            )
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'),
            );
    }

    private function findTask(int $task): Task
    {
        return Task::query()
            ->with('user:id,name,email,role')
            ->findOrFail($task);
    }
}
