<?php

use App\Models\User;
use App\Models\Organization;
use function Pest\Laravel\actingAs;
use Livewire\Volt\Volt;

describe('Organization Servers', function () {
    it('allows organization members to create a server with a valid hostname', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->currentOrganization;

        Volt::actingAs($user)->test('servers')
            ->set('name', 'valid-hostname.example.com')
            ->call('createServer')
            ->assertHasNoErrors();

        // Expect server exists in organization (implementation will need Server model)
        // $server = $organization->servers()->where('name', 'valid-hostname.example.com')->first();
        // expect($server)->not->toBeNull();

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

        // Pretend server already exists (implementation will need Server model/factory)
        // Server::factory()->for($organization)->create(['name' => 'duplicate.example.com']);

        Volt::actingAs($user)->test('servers')
            ->set('name', 'duplicate.example.com')
            ->call('createServer')
            ->assertHasErrors(['name']);
    });
});
