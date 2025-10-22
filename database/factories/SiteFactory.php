<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
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
            'hostname' => fake()->domainName,
            'php_version' => fake()->randomElement(['8.4', '8.3', '8.1']),
            'repository_url' => 'git@github.com:laravel/laravel.git',
            'repository_branch' => 'main',
        ];
    }

    /**
     * Indicate that the site has not had Caddy installed.
     */
    public function caddyNotInstalled(): static
    {
        return $this->state(fn (array $attributes) => [
            'caddy_installed_at' => null,
        ]);
    }
}
