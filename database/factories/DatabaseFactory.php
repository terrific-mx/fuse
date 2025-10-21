<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Database>
 */
class DatabaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'server_id' => \App\Models\Server::factory(),
            'name' => $this->faker->unique()->word,
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the database is installed.
     */
    public function installed(): static
    {
        return $this->state(fn () => [
            'status' => 'installed',
        ]);
    }
}
