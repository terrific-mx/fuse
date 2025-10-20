<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
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
            'created_by' => User::factory(),
            'name' => $this->faker->word(),
            'status' => 'pending',
            'ip_address' => $this->faker->ipv4(),
            'sudo_password' => $this->faker->password(),
            'database_password' => $this->faker->password(),
            'memory' => 512,
        ];
    }

    /**
     * Indicate that the server status is pending.
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the server status is provisioned.
     */
    public function provisioned(): static
    {
        return $this->state(fn () => [
            'status' => 'provisioned',
        ]);
    }

    /**
     * Indicate that the server has no public SSH key.
     */
    public function withoutPublicSshKey(): static
    {
        return $this->state(fn () => [
            'public_ssh_key' => null,
        ]);
    }
}
