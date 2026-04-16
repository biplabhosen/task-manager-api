<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getAllForUser(User $user, array $filters = []): LengthAwarePaginator;

    public function createForUser(User $user, array $data): Task;

    public function findForUser(User $user, int $taskId): Task;

    public function updateForUser(User $user, int $taskId, array $data): Task;

    public function deleteForUser(User $user, int $taskId): void;

    public function markAsCompletedForUser(User $user, int $taskId): Task;
}
