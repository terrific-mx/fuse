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
            'name' => 'Task Name',
            'user' => 'root',
            'status' => 'finished',
            'exit_code' => 0,
            'script' => '',
            'output' => '',
            'options' => [],
        ];
    }

    public function running(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'running',
            ];
        });
    }

    public function finished(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'finished',
            ];
        });
    }
}
