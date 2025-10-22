<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cronjob>
 */
class CronjobFactory extends Factory
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
            'command' => fake()->sentence,
            'user' => fake()->userName,
            'frequency' => fake()->randomElement([
                'every_minute', 'every_5_minutes', 'hourly', 'daily', 'weekly', 'monthly', 'on_reboot', 'custom',
            ]),
            'custom_expression' => null,
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the cronjob is pending.
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the cronjob is installed.
     */
    public function installed(): static
    {
        return $this->state(fn () => [
            'status' => 'installed',
        ]);
    }

    /**
     * Indicate that the cronjob is failed.
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
        ]);
    }
}
