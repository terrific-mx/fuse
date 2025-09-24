<?php

use App\Models\Server;
use App\Models\ServerCredential;
use App\Models\User;
use Facades\App\Services\HetznerService;
use Livewire\Volt\Volt;

beforeEach(function () {
    HetznerService::shouldReceive('getLocations')
        ->andReturn([['name' => 'fsn1', 'city' => 'Falkenstein']]);
    HetznerService::shouldReceive('getServerTypes')
        ->andReturn([
            [
                'name' => 'cpx11',
                'architecture' => 'x86',
                'cores' => 2,
                'cpu_type' => 'shared',
                'description' => 'CPX 11',
                'disk' => 40,
                'memory' => 2,
                'locations' => ['fsn1'],
            ],
        ]);
});

it('creates a server with valid hostname and credential for organization member', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    $credential = ServerCredential::factory()->for($organization)->create([
        'provider' => 'hetzner',
        'credentials' => ['api_key' => 'test-key'],
    ]);

    HetznerService::shouldReceive('createServer')
        ->andReturn([
            'provider_id' => '12345',
            'ip_address' => '192.0.2.1',
            'status' => 'running',
        ]);

    Volt::actingAs($user)->test('servers')
        ->set('name', 'valid-hostname')
        ->set('location', 'fsn1')
        ->set('serverType', 'cpx11')
        ->set('credential', $credential->id)
        ->call('createServer')
        ->assertHasNoErrors();

    $server = $organization->servers()->where('name', 'valid-hostname')->first();
    expect($server)->not->toBeNull();
    expect($server->provider_id)->not->toBeNull();
    expect($server->ip_address)->not->toBeNull();
    expect($server->status)->not->toBeNull();
    expect($server->server_credential_id)->toBe($credential->id);
});

it('fails with validation error when credential is missing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('credential', '')
        ->call('createServer')
        ->assertHasErrors(['credential' => 'required']);
});

it('fails with validation error when credential does not belong to organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $otherCredential = ServerCredential::factory()->create([
        'provider' => 'hetzner',
        'credentials' => ['api_key' => 'other-key'],
    ]);

    Volt::actingAs($user)->test('servers')
        ->set('credential', $otherCredential->id)
        ->call('createServer')
        ->assertHasErrors(['credential' => 'exists']);
});

it('fails with validation error when credential provider is incorrect', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    $wrongProviderCredential = ServerCredential::factory()->for($organization)->create([
        'provider' => 'aws',
        'credentials' => ['api_key' => 'aws-key'],
    ]);

    Volt::actingAs($user)->test('servers')
        ->set('credential', $wrongProviderCredential->id)
        ->set('serverType', 'cpx11')
        ->set('location', 'fsn1')
        ->set('name', 'valid-hostname')
        ->call('createServer')
        ->assertHasErrors(['credential' => 'exists']);
});

it('shows validation error if server name is missing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('name', '')
        ->call('createServer')
        ->assertHasErrors(['name' => 'required']);
});

it('fails with validation error when server name is not a valid hostname', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('name', 'invalid_hostname!')
        ->call('createServer')
        ->assertHasErrors(['name' => 'alpha_dash']);
});

it('fails with validation error when server name is not unique within organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    Server::factory()->for($organization)->create([
        'name' => 'duplicate.example.com',
    ]);

    Volt::actingAs($user)->test('servers')
        ->set('name', 'duplicate.example.com')
        ->call('createServer')
        ->assertHasErrors(['name']);
});

it('fails with validation error when location is missing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('location', '')
        ->call('createServer')
        ->assertHasErrors(['location' => 'required']);
});

it('fails with validation error when location is not allowed', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)->test('servers')
        ->set('location', 'invalid-location')
        ->call('createServer')
        ->assertHasErrors(['location' => 'in']);
});

it('shows validation error if serverType is missing', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('serverType', '')
        ->call('createServer')
        ->assertHasErrors(['serverType' => 'required']);
});

it('fails with validation error when server type is not allowed', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    Volt::actingAs($user)->test('servers')
        ->set('location', 'fsn1')
        ->set('serverType', 'invalid-type')
        ->call('createServer')
        ->assertHasErrors(['serverType' => 'in']);
});

it('deletes a server for organization member', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    $server = Server::factory()->for($organization)->create([
        'name' => 'delete-me.example.com',
    ]);

    Volt::actingAs($user)->test('servers')
        ->call('deleteServer', $server);

    expect($organization->servers()->where('id', $server->id)->exists())->toBeFalse();
});

it('does not create server and shows error when Hetzner returns error', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;

    HetznerService::shouldReceive('createServer')
        ->andReturn(['error' => 'API key invalid']);

    Volt::actingAs($user)->test('servers')
        ->set('name', 'fail-hostname')
        ->set('location', 'fsn1')
        ->set('serverType', 'cpx11')
        ->call('createServer');

    $server = $organization->servers()->where('name', 'fail-hostname')->first();

    expect($server)->toBeNull();
});
