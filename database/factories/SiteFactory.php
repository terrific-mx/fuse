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
            'hostname' => $this->faker->domainName,
            'php_version' => $this->faker->randomElement(['8.4', '8.3', '8.1']),
            'type' => $this->faker->randomElement(['Generic', 'Laravel', 'Static', 'Wordpress']),
            'web_folder' => '/public',
            'repository_url' => 'git@github.com:laravel/laravel.git',
            'repository_branch' => 'main',
            'use_deploy_key' => true,
        ];
    }
}
