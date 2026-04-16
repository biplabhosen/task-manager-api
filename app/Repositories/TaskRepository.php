<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->tasks()->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
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
