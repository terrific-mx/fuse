<?php

namespace Database\Factories;

use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class FirewallRuleFactory extends Factory
{
    protected $model = FirewallRule::class;

    public function definition(): array
    {
        return [
            'server_id' => Server::factory(),
            'name' => $this->faker->words(2, true),
            'action' => $this->faker->randomElement(['allow', 'deny', 'reject']),
            'port' => $this->faker->numberBetween(1, 65535),
            'from_ip_address' => $this->faker->optional()->ipv4(),
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
