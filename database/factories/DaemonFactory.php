<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Daemon>
 */
class DaemonFactory extends Factory
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
            'command' => 'php artisan queue:work',
            'user' => 'fuse',
            'processes' => 2,
            'stop_wait_seconds' => 10,
            'stop_signal' => 'TERM',
            'status' => 'pending',
        ];
    }
}
