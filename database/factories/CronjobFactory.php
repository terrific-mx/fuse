<?php

namespace Database\Factories;

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
            'server_id' => \App\Models\Server::factory(),
            'command' => $this->faker->sentence,
            'user' => $this->faker->userName,
            'expression' => '* * * * *',
        ];
    }
}
