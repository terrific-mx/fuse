<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\User;
use App\ShellResult;
use Facades\App\ShellProcessRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can create the server to provision', function () {
    Queue::fake();

    $user = User::factory()->create();
    $serverProvider = ServerProvider::factory()->for($user)->create();

    $component = Volt::actingAs($user)->test('pages.servers.provision')
        ->set('name', 'my-server')
        ->set('server_provider_id', $serverProvider->id)
        ->set('size', 's-1vcpu-512mb-10gb')
        ->set('region', 'nyc1')
        ->call('provision');

    expect($user->servers)->toHaveCount(1);

    expect($user->servers->first())
        ->name->toBe('my-server')
        ->size->toBe('s-1vcpu-512mb-10gb')
        ->region->toBe('nyc1')
        ->username->toBe('fuse')
        ->sudo_password->toBeString()
        ->database_password->toBeString()
        ->status->toBe('creating');

    Queue::assertPushed(ProvisionServer::class);
});

it('creates the server on the server provider', function () {
    $user = User::factory()->create();
    $serverProvider = ServerProvider::factory()->for($user)->create();
    ShellProcessRunner::mock([
        new ShellResult(exitCode: 0, output: 'root'), // GetCurrentDirectory Script Result
    ]);

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('name', 'my-server')
        ->set('server_provider_id', $serverProvider->id)
        ->set('size', 's-1vcpu-512mb-10gb')
        ->set('region', 'nyc1')
        ->call('provision');

    expect($user->servers->first())
        ->provider_server_id->not->toBeNull();
});

it('validates requried fields', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('name', '')
        ->set('size', '')
        ->set('region', '')
        ->set('server_provider_id', '')
        ->call('provision')
        ->assertHasErrors(['name', 'size', 'region', 'server_provider_id']);
});

it('validates server_provider_id belongs to the user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherUsersProvider = ServerProvider::factory()->for($otherUser)->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('server_provider_id', $otherUsersProvider->id)
        ->call('provision')
        ->assertHasErrors(['server_provider_id']);
});

it('validates server_provider_id exists', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('server_provider_id', 999)
        ->call('provision')
        ->assertHasErrors(['server_provider_id']);
});

it('validates name only contains alpha_dash characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('name', 'my-server!')
        ->call('provision')
        ->assertHasErrors(['name']);
});

it('validates name is unique for the user', function () {
    $user = User::factory()->create();
    Server::factory()->for($user)->create([
        'name' => 'my-server',
    ]);

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('name', 'my-server')
        ->call('provision')
        ->assertHasErrors(['name']);
});

it('validates region to be valid', function () {
    $user = User::factory()->create();
    $serverProvider = ServerProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('server_provider_id', $serverProvider->id)
        ->set('region', 'invalid-region')
        ->call('provision')
        ->assertHasErrors(['region']);
});

it('validates size to be valid', function () {
    $user = User::factory()->create();
    $serverProvider = ServerProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.provision')
        ->set('server_provider_id', $serverProvider->id)
        ->set('size', 'invalid-size')
        ->call('provision')
        ->assertHasErrors(['size']);
});
