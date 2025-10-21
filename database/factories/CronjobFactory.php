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
            'command' => $this->faker->sentence,
            'user' => $this->faker->userName,
            'frequency' => $this->faker->randomElement([
                'every_minute', 'every_5_minutes', 'hourly', 'daily', 'weekly', 'monthly', 'on_reboot', 'custom',
            ]),
            'custom_expression' => null,
        ];
    }
}
