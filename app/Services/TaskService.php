<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function getUserTasks(User $user): Collection
    {
        return $this->taskRepository->getAllForUser($user);
    }

    public function createTask(User $user, array $data): Task
    {
        return $this->taskRepository->createForUser($user, $data);
    }

    public function getTask(User $user, int $taskId): Task
    {
        return $this->taskRepository->findForUser($user, $taskId);
    }

    public function updateTask(User $user, int $taskId, array $data): Task
    {
        return $this->taskRepository->updateForUser($user, $taskId, $data);
    }

    public function deleteTask(User $user, int $taskId): void
    {
        $this->taskRepository->deleteForUser($user, $taskId);
    }
}
