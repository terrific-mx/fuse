<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\ServerCredential;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Volt\Volt;
use Facades\App\Services\HetznerService;
use function Pest\Laravel\actingAs;

describe('Organization Credentials', function () {
    it('validates Hetzner API key by fetching regions (success)', function () {
        HetznerService::shouldReceive('validateApiKey')->with('valid-key')->andReturn(true);

        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->organizations()->first();

        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', 'hetzner')
            ->set('name', 'Valid Hetzner')
            ->set('credentials', ['api_key' => 'valid-key'])
            ->call('addCredential')
            ->assertHasNoErrors();

        $credential = $organization->serverCredentials()->where('provider', 'hetzner')->first();
        expect($credential)->not->toBeNull();
        expect($credential->name)->toBe('Valid Hetzner');
    });

    it('shows validation error for invalid Hetzner API key', function () {
        HetznerService::shouldReceive('validateApiKey')->with('invalid-key')->andReturn(false);

        $user = User::factory()->withPersonalOrganization()->create();

        Volt::actingAs($user)->test('server-credentials')
            ->set('provider', 'hetzner')
            ->set('name', 'Invalid Hetzner')
            ->set('credentials', ['api_key' => 'invalid-key'])
            ->call('addCredential')
            ->assertHasErrors(['credentials.api_key' => __('The Hetzner API key is invalid.')]);
    });

    it('can access the server-credentials page', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        actingAs($user)
            ->get('/server-credentials')
            ->assertStatus(200);
    });

    it('allows organization members to add Hetzner credentials', function () {
        HetznerService::shouldReceive('validateApiKey')->with('test-key')->andReturn(true);

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

    it('allows organization members to delete a server credential', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $organization = $user->organizations()->first();
        $credential = ServerCredential::factory()->for($organization)->create();

        Volt::actingAs($user)->test('server-credentials')
            ->call('deleteCredential', $credential->id);

        expect($organization->serverCredentials()->find($credential->id))->toBeNull();
    });

    it('prevents users from deleting credentials in organizations they do not belong to', function () {
        $user = User::factory()->withPersonalOrganization()->create();
        $otherOrg = Organization::factory()->create();
        $credential = ServerCredential::factory()->for($otherOrg)->create([
            'provider' => 'hetzner',
            'name' => 'Other Hetzner',
            'credentials' => ['api_key' => 'other-key'],
        ]);

        Volt::actingAs($user)->test('server-credentials')
            ->call('deleteCredential', $credential->id)
            ->assertForbidden();

        expect($otherOrg->serverCredentials()->find($credential->id))->not->toBeNull();
    });

    it('throws ModelNotFoundException when attempting to delete a non-existent credential', function () {
        $user = User::factory()->withPersonalOrganization()->create();

        Volt::actingAs($user)->test('server-credentials')
            ->call('deleteCredential', 999999);
    })->throws(ModelNotFoundException::class);
});
