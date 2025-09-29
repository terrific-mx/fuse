<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FirewallRule>
 */
class FirewallRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'server_id' => fn() => \App\Models\Server::factory(),
            'name' => $this->faker->word(),
            'action' => $this->faker->randomElement(['allow', 'deny', 'reject']),
            'port' => $this->faker->numberBetween(1, 65535),
            'from_ip' => $this->faker->ipv4(),
        ];
    }
}
