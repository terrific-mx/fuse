<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\ServerProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Server>
 */
class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'provider_id' => ServerProvider::factory(),
            'name' => $this->faker->word(),
            'type' => 'test-type',
            'region' => 'test-region',
            'status' => 'pending',
        ];
    }
}
