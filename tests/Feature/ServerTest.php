<?php

use App\Models\User;
use App\Models\Server;
use App\Models\ServerCredential;
use Livewire\Volt\Volt;

describe('Organization Servers', function () {
    it('allows organization members to create a server with a valid hostname', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->currentOrganization;
         ServerCredential::factory()->for($organization)->create([
             'provider' => 'hetzner',
             'credentials' => ['api_key' => 'test-key'],
         ]);

        \Facades\App\Services\HetznerService::shouldReceive('getLocations')
            ->andReturn([
                ['name' => 'fsn1', 'city' => 'Falkenstein'],
                ['name' => 'nbg1', 'city' => 'Nuremberg'],
            ]);
        \Facades\App\Services\HetznerService::shouldReceive('getServerTypes')
            ->andReturn([
                [
                    'name' => 'cpx11',
                    'architecture' => 'x86',
                    'cores' => 2,
                    'cpu_type' => 'shared',
                    'description' => 'CPX 11',
                    'disk' => 40,
                    'memory' => 2,
                    'locations' => ['fsn1', 'nbg1'],
                ],
            ]);
        \Facades\App\Services\HetznerService::shouldReceive('createServer')
            ->andReturn([
                'hetzner_id' => '12345',
                'ip_address' => '192.0.2.1',
                'status' => 'running',
            ]);

        Volt::actingAs($user)->test('servers')
            ->set('name', 'valid-hostname')
            ->set('serverType', 'cpx11')
            ->set('location', 'fsn1')
            ->call('createServer')
            ->assertHasNoErrors();

        $server = $organization->servers()->where('name', 'valid-hostname')->first();

        expect($server)->not->toBeNull();
    });

    it('shows validation error if server name is missing', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        Volt::actingAs($user)->test('servers')
            ->set('name', '')
            ->call('createServer')
            ->assertHasErrors(['name']);
    });

    it('shows validation error if server name is not a valid hostname', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        Volt::actingAs($user)->test('servers')
            ->set('name', 'invalid_hostname!')
            ->call('createServer')
            ->assertHasErrors(['name']);
    });

    it('shows validation error if server name is not unique within organization', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->currentOrganization;
        Server::factory()->for($organization)->create(['name' => 'duplicate.example.com']);

        Volt::actingAs($user)->test('servers')
            ->set('name', 'duplicate.example.com')
            ->call('createServer')
            ->assertHasErrors(['name']);
    });

    it('allows organization members to delete a server', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->currentOrganization;
        $server = Server::factory()->for($organization)->create(['name' => 'delete-me.example.com']);

        Volt::actingAs($user)->test('servers')
            ->call('deleteServer', $server);

        expect($organization->servers()->where('id', $server->id)->exists())->toBeFalse();
    });
});
