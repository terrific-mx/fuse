<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deployment>
 */
class DeploymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'status' => 'pending',
            'triggered_by' => User::factory(),
            'commit' => $this->faker->sha1(),
            'deployed_at' => null,
        ];
    }

    /**
     * Indicate that the deployment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    /**
     * Indicate that the deployment is deploying.
     */
    public function deploying(): static
    {
        return $this->state(fn () => ['status' => 'deploying']);
    }

    /**
     * Indicate that the deployment is deployed.
     */
    public function deployed(): static
    {
        return $this->state(fn () => ['status' => 'deployed']);
    }
}
