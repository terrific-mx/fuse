<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\SourceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
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
            'source_provider_id' => SourceProvider::factory(),
            'domain' => 'example.com',
            'repository' => 'example/valid-repository',
            'branch' => 'valid-branch',
            'web_directory' => 'public',
            'status' => 'installed',
            'tls' => 'auto',
            'type' => 'laravel',
            'shared_directories' => [],
            'writeable_directories' => [],
            'shared_files' => [],
        ];
    }
}
