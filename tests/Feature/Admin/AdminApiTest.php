<?php

namespace Tests\Feature\Admin;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users_and_all_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Task::factory()->for($admin)->create([
            'title' => 'Admin task',
            'status' => Task::STATUS_PENDING,
        ]);
        Task::factory()->for($user)->completed()->create([
            'title' => 'User completed task',
            'due_date' => '2026-04-20',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/users')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 2);

        $this->getJson('/api/admin/tasks?status=completed')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'User completed task')
            ->assertJsonPath('data.0.user.role', User::ROLE_USER);
    }

    public function test_admin_can_update_and_delete_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'User task',
            'status' => Task::STATUS_PENDING,
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/tasks/{$task->id}", [
            'title' => 'Admin updated task',
            'status' => Task::STATUS_COMPLETED,
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Admin updated task')
            ->assertJsonPath('data.status', Task::STATUS_COMPLETED)
            ->assertJsonPath('data.user.id', $user->id);

        $this->deleteJson("/api/admin/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/admin/users/{$user->id}/role", [
            'role' => User::ROLE_ADMIN,
        ])
            ->assertOk()
            ->assertJsonPath('data.role', User::ROLE_ADMIN);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_non_admin_user_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/admin/users')
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->patchJson('/api/admin/users/1/role', [
            'role' => User::ROLE_ADMIN,
        ])
            ->assertForbidden();
    }
}
