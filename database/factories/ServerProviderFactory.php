<?php

namespace Database\Factories;

use App\Models\Organization;
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
            'organization_id' => Organization::factory(),
            'provider' => 'hetzner',
            'name' => $this->faker->word . ' Hetzner',
            'credentials' => [
                'api_key' => $this->faker->sha256,
            ],
        ];
    }
}
