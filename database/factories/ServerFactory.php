<?php

namespace Database\Factories;

use App\Models\ServerProvider;
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
        $user = User::factory();
        $serverProvider = ServerProvider::factory()->for($user);

        return [
            'user_id' => $user,
            'server_provider_id' => $serverProvider,
            'name' => 'my-server',
            'size' => 's-1vcpu-512mb-10gb',
            'region' => 'nyc1',
            'status' => 'provisioned',
            'username' => 'fuse',
            'sudo_password' => 'password',
            'database_password' => 'password',
        ];
    }

    public function provisioning(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'provisioning',
            ];
        });
    }

    public function provisioned(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'provisioned',
            ];
        });
    }

    public function creating(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'creating',
            ];
        });
    }

    public function withPublicAddress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'public_address' => '192.168.1.1',
            ];
        });
    }
}
