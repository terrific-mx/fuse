<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'server_id' => Server::factory(),
            'payload' => [],
            'status' => 'pending',
            'after_actions' => [],
        ];
    }

    /**
     * Indicate that the task was successful (exit_code 0).
     */
    public function successful(): static
    {
        return $this->state(fn () => [
            'exit_code' => 0,
        ]);
    }

    /**
     * Indicate that the task failed (exit_code non-zero).
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'exit_code' => 1,
        ]);
    }
}

