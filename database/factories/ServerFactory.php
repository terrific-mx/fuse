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
            'server_provider_id' => ServerProvider::factory(),
            'name' => $this->faker->domainName,
            'provider_id' => $this->faker->uuid,
            'ip_address' => $this->faker->ipv4,
            'status' => 'running',
        ];
    }
}
