<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\ServerCredential;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs;

describe('Organization Credentials', function () {
    it('can access the server-credentials page', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        actingAs($user)
            ->get('/server-credentials')
            ->assertStatus(200);
    });

    it('allows organization members to add Hetzner credentials', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->organizations()->first();

        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', 'hetzner')
            ->set('name', 'My Hetzner')
            ->set('credentials', ['api_key' => 'test-key'])
            ->call('addCredential');

        $credential = $organization->serverCredentials()->first();

        expect($credential)->not->toBeNull();
        expect($credential->provider)->toBe('hetzner');
        expect($credential->name)->toBe('My Hetzner');
    });

    it('validates required fields when adding credentials', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', '')
            ->set('name', '')
            ->set('credentials', [])
            ->call('addCredential')
            ->assertHasErrors(['provider', 'name', 'credentials']);
    });

    it('enforces unique provider credentials per organization', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->organizations()->first();
        ServerCredential::factory()->for($organization)->create([
            'provider' => 'hetzner',
            'name' => 'My Hetzner',
            'credentials' => ['api_key' => 'test-key'],
        ]);

        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', 'hetzner')
            ->set('name', 'Another Hetzner')
            ->set('credentials', ['api_key' => 'another-key'])
            ->call('addCredential')
            ->assertHasErrors(['provider']);
    });

    it('rejects non-Hetzner providers when adding credentials', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', 'invalid-provider')
            ->set('name', 'Test Credential')
            ->set('credentials', ['api_key' => 'test-key'])
            ->call('addCredential')
            ->assertHasErrors(['provider']);
    });
});
