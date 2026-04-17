<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $dueDate = fake()->boolean(80)
            ? fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d')
            : null;

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(Task::STATUSES),
            'due_date' => $dueDate,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => Task::STATUS_PENDING,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => Task::STATUS_IN_PROGRESS,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => Task::STATUS_COMPLETED,
        ]);
    }
}
