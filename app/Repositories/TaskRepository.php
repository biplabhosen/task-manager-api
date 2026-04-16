<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $this->queryForUser($user)
            ->when(
                ! empty($filters['status']),
                fn (Builder $builder) => $builder->where('status', $filters['status']),
            )
            ->when(
                ! empty($filters['due_date']),
                fn (Builder $builder) => $builder->whereDate('due_date', $filters['due_date']),
            )
            ->when(
                ! empty($filters['search']),
                fn (Builder $builder) => $builder->where('title', 'like', '%'.$filters['search'].'%'),
            )
            ->latest();

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    public function createForUser(User $user, array $data): Task
    {
        $task = $user->tasks()->create($data);

        return $this->findForUser($user, $task->id);
    }

    public function findForUser(User $user, int $taskId): Task
    {
        return $this->queryForUser($user)->findOrFail($taskId);
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

    public function markAsCompletedForUser(User $user, int $taskId): Task
    {
        return $this->updateForUser($user, $taskId, [
            'status' => Task::STATUS_COMPLETED,
        ]);
    }

    private function queryForUser(User $user): Builder
    {
        return $user->tasks()->select([
            'id',
            'user_id',
            'title',
            'description',
            'status',
            'due_date',
            'created_at',
            'updated_at',
        ]);
    }
}
