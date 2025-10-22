<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class FirewallRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'server_id' => Server::factory(),
            'name' => fake()->words(2, true),
            'action' => fake()->randomElement(['allow', 'deny', 'reject']),
            'port' => fake()->numberBetween(1, 65535),
            'from_ip_address' => fake()->optional()->ipv4(),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function installed(): static
    {
        return $this->state(fn () => ['status' => 'installed']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed']);
    }
}
