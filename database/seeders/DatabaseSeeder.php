<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\Site;
use App\Models\SourceProvider;
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
        $organization = $user->currentOrganization;

        $this->seedSshKeysForOrganization($organization);
        $this->seedServerProviders($organization);
        $this->seedSourceProviders($organization);
        $this->seedServersForOrganization($organization);
    }

    /**
     * Seed a few SSH keys for the given organization.
     */
    private function seedSshKeysForOrganization($organization): void
    {
        $keys = [
            [
                'name' => 'Work Laptop',
                'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey1',
            ],
            [
                'name' => 'Home Desktop',
                'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIrandomkey2',
            ],
            [
                'name' => 'Server Key',
                'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey3',
            ],
        ];

        foreach ($keys as $key) {
            $organization->sshKeys()->create($key);
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

    /**
     * Seed server providers for the given organization.
     */
    private function seedServerProviders($organization): void
    {
        foreach ($this->serverProviderSeedData() as $provider) {
            ServerProvider::factory()->for($organization)->create($provider);
        }
    }

    /**
     * Seed source providers for the given organization.
     */
    private function seedSourceProviders($organization): void
    {
        foreach ($this->sourceProviderSeedData() as $provider) {
            SourceProvider::factory()->for($organization)->create($provider);
        }
    }

    /**
     * Seed a few servers for the given organization and its providers.
     */
    private function seedServersForOrganization($organization): void
    {
        $providers = $organization->serverProviders;

        $servers = [
            [
                'name' => 'Web Server',
                'type' => 'cx21',
                'region' => 'fsn1',
                'status' => 'pending',
                'provider_id' => $providers[0]->id ?? null,
                'organization_id' => $organization->id,
            ],
            [
                'name' => 'API Server',
                'type' => 'cx21',
                'region' => 'fsn1',
                'status' => 'pending',
                'provider_id' => $providers[1]->id ?? null,
                'organization_id' => $organization->id,
            ],
            [
                'name' => 'Worker Server',
                'type' => 'cx21',
                'region' => 'fsn1',
                'status' => 'pending',
                'provider_id' => $providers[2]->id ?? null,
                'organization_id' => $organization->id,
            ],
        ];

        foreach ($servers as $serverData) {
            $server = Server::factory()->create($serverData);
            $this->seedSitesForServer($server);
        }
    }

    /**
     * Seed 3 sites for the given server.
     */
    private function seedSitesForServer(Server $server): void
    {
        foreach (range(1, 3) as $i) {
            Site::factory()->create([
                'server_id' => $server->id,
                'hostname' => $server->name . '-site' . $i . '.example.com',
                'repository_branch' => $i === 1 ? 'main' : ($i === 2 ? 'develop' : 'feature-x'),
            ]);
        }
    }

    /**
     * Get the seed data for server providers.
     *
     * @return array<int, array<string, mixed>>
     */
    private function serverProviderSeedData(): array
    {
        return [
            [
                'name' => 'Hetzner Cloud',
                'type' => 'Hetzner Cloud',
                'meta' => ['token' => 'hetzner-token-123'],
            ],
            [
                'name' => 'AWS',
                'type' => 'aws',
                'meta' => ['token' => 'aws-token-456'],
            ],
            [
                'name' => 'Custom Provider',
                'type' => 'custom',
                'meta' => ['token' => 'custom-token-789'],
            ],
        ];
    }

    /**
     * Get the seed data for source providers.
     *
     * @return array<int, array<string, mixed>>
     */
    private function sourceProviderSeedData(): array
    {
        return [
            [
                'name' => 'GitHub',
                'type' => 'GitHub',
                'meta' => ['token' => 'github-token-abc'],
            ],
            [
                'name' => 'GitLab',
                'type' => 'GitLab',
                'meta' => ['token' => 'gitlab-token-def'],
            ],
            [
                'name' => 'Bitbucket',
                'type' => 'Bitbucket',
                'meta' => ['token' => 'bitbucket-token-ghi'],
            ],
        ];
    }
}
