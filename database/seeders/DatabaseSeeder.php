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
        $user = $this->seedUser();

        // Get the user's personal organization
        $organization = $user->organizations()->where('personal', true)->first();

        // Create 2 servers for this organization and user
        $servers = \App\Models\Server::factory()
            ->count(2)
            ->create([
                'organization_id' => $organization->id,
                'created_by' => $user->id,
            ]);

        foreach ($servers as $server) {
            // Create 2 sites per server
            $sites = \App\Models\Site::factory()
                ->count(2)
                ->create([
                    'server_id' => $server->id,
                ]);

            foreach ($sites as $site) {
                // Create 2 deployments per site, triggered by the test user
                \App\Models\Deployment::factory()
                    ->count(2)
                    ->create([
                        'site_id' => $site->id,
                        'triggered_by' => $user->id,
                    ]);
            }
        }
    }

    /**
     * Seed the test user with personal organization and subscription.
     */
    private function seedUser(): User
    {
        return User::factory()->withPersonalOrganizationAndSubscription()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
