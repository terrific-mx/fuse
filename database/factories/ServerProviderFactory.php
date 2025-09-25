<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServerProvider>
 */
class ServerProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['aws', 'azure', 'gcp', 'custom']),
            'meta' => [
                'region' => $this->faker->stateAbbr,
                'api_key' => $this->faker->uuid,
            ],
        ];
    }
}
