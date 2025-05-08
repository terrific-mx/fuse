<?php

use App\Jobs\InstallApplication;
use App\Models\Server;
use App\Models\SourceProvider;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can create an application with valid fields', function () {
    Process::fake();

    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    expect($server->applications)->toHaveCount(1);
});

it('requires all required fields to be filled out', function () {
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', '')
        ->set('source_provider_id', '')
        ->set('repository', '')
        ->set('branch', '')
        ->set('web_directory', '')
        ->call('create')
        ->assertHasErrors(['domain', 'repository', 'branch', 'source_provider_id']);
});

it('prevents the use of a source provider owned by another user', function () {
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $anotherUserSourceProvider = SourceProvider::factory()->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('source_provider_id', $anotherUserSourceProvider->id)
        ->call('create')
        ->assertHasErrors(['source_provider_id']);
});

it('rejects invalid repositories and ensures proper error handling', function () {
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/invalid-repository')
        ->call('create')
        ->assertHasErrors(['repository']);
});

it('rejects invalid branches and ensures proper error handling', function () {
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'invalid-branch')
        ->call('create')
        ->assertHasErrors(['branch']);
});

it('creates a task to install the Caddyfile on the server after application creation', function () {
    Process::fake();

    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    expect($server->tasks->first())->name->toBe('Installing Caddyfile');
});

it('creates a task to update Caddy imports on the server after application creation', function () {
    Process::fake();

    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    expect($server->tasks->get(1))
        ->name->toBe('Updating Caddy Imports')
        ->script->toContain('import /home/fuse/example.com/Caddyfile');
});

it('marks the application as installed after successful creation', function () {
    Process::fake();

    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    $application = $server->applications->first();
    expect($application->status)->toBe('installed');
});

it('dispatches a job to install the application on the server after successful creation', function () {
    Queue::fake();
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    Queue::assertPushed(InstallApplication::class);
    expect($server->applications()->first())->status->toBe('creating');
});

it('stores a new deployment after successful application installation', function () {
    Process::fake();
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    expect($server->applications()->first()->deployments)->toHaveCount(1);
    expect($server->applications()->first()->deployments()->first())->status->toBe('pending');
});

it('creates a task to run script to deploy the laravel application without downtime', function () {
    Process::fake();
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    expect($server->tasks->last())
        ->name->toBe('Deploying application without downtime')
        ->user->toBe('fuse');
});

it('marks the deployment as finished after successful task deployment', function () {
    Process::fake();
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    tap($server->tasks->last(), function (Task $task) {
        expect($task)->name->toBe('Deploying application without downtime');

        $task->finish(exitCode: 0);
    });

    expect($server->applications()->first()->deployments->last())
        ->status->toBe('finished');
});

it('marks the deployment as failed on failed task deployment', function () {
    Process::fake();
    $user = User::factory()->create();
    $server = Server::factory()->for($user)->create();
    $sourceProvider = SourceProvider::factory()->for($user)->create();

    Volt::actingAs($user)->test('pages.servers.applications.create', ['server' => $server])
        ->set('domain', 'example.com')
        ->set('source_provider_id', $sourceProvider->id)
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', '/public')
        ->call('create');

    tap($server->tasks->last(), function (Task $task) {
        expect($task)->name->toBe('Deploying application without downtime');

        $task->finish(exitCode: 999);
    });

    expect($server->applications()->first()->deployments->last())
        ->status->toBe('failed');
});
