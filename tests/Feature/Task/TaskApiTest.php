<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_update_complete_and_delete_task(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/tasks', [
            'title' => 'Finish assignment',
            'description' => 'Complete the Laravel task manager API.',
            'status' => Task::STATUS_PENDING,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $taskId = $createResponse->json('data.id');

        $createResponse
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Finish assignment');

        $this->putJson("/api/tasks/{$taskId}", [
            'title' => 'Finish technical assignment',
            'status' => Task::STATUS_IN_PROGRESS,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', Task::STATUS_IN_PROGRESS);

        $this->patchJson("/api/tasks/{$taskId}/complete")
            ->assertOk()
            ->assertJsonPath('data.status', Task::STATUS_COMPLETED);

        $this->deleteJson("/api/tasks/{$taskId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('tasks', [
            'id' => $taskId,
        ]);
    }

    public function test_user_only_sees_their_own_tasks_and_can_filter_results(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        Task::factory()->for($user)->completed()->create([
            'title' => 'My completed task',
            'due_date' => '2026-04-20',
        ]);
        Task::factory()->for($user)->pending()->create([
            'title' => 'My pending task',
            'due_date' => '2026-04-21',
        ]);
        Task::factory()->for($otherUser)->completed()->create([
            'title' => 'Other user completed task',
            'due_date' => '2026-04-20',
        ]);

        $response = $this->getJson('/api/tasks?status=completed&search=completed&due_date=2026-04-20');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'My completed task')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_user_cannot_access_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/tasks/{$task->id}")
            ->assertNotFound();
    }
}
