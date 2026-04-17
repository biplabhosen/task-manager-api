<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(5)
            ->create()
            ->each(fn (User $user) => Task::factory(rand(3, 8))->create([
                'user_id' => $user->id,
            ]));
    }
}
