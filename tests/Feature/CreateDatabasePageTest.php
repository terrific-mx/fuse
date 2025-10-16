<?php

use App\Jobs\InstallDatabaseJob;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to view the create database page for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.databases.create', ['server' => $server])
        ->assertOk();
});

it('creates a database for a server', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.databases.create', ['server' => $server])
        ->set('name', 'my_database')
        ->call('create')
        ->assertHasNoErrors();

    $database = $server->databases()->first();

    expect($database)->not->toBeNull();
    expect($database->name)->toBe('my_database');
});

it('dispatches InstallDatabaseJob when a database is created', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.databases.create', ['server' => $server])
        ->set('name', 'my_database')
        ->call('create')
        ->assertHasNoErrors();

    $database = $server->databases()->first();

    Queue::assertPushed(InstallDatabaseJob::class, function ($job) use ($database) {
        return $job->database->is($database);
    });
});
