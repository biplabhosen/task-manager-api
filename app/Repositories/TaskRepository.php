<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllForUser(User $user): Collection
    {
        return $user->tasks()
            ->latest()
            ->get();
    }

    public function createForUser(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    public function findForUser(User $user, int $taskId): Task
    {
        return $user->tasks()->findOrFail($taskId);
    }

    public function updateForUser(User $user, int $taskId, array $data): Task
    {
        $task = $this->findForUser($user, $taskId);

        $task->update($data);

        return $task->fresh();
    }

    public function deleteForUser(User $user, int $taskId): void
    {
        $task = $this->findForUser($user, $taskId);

        $task->delete();
    }
}
