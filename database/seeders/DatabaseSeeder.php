<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->withPersonalOrganizationAndSubscription([
            'ssh_public_key' => env('TEST_SSH_PUBLIC_KEY'),
            'ssh_private_key' => env('TEST_SSH_PRIVATE_KEY'),
        ])->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $organization = $user->currentOrganization;

        if ($organization) {
            $organization->serverProviders()->create([
                'provider' => 'hetzner',
                'name' => 'Test Provider',
                'credentials' => [
                    'api_key' => env('TEST_SERVER_PROVIDER_API_KEY'),
                ],
            ]);
        }
    }
}
