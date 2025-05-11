<?php

use App\Jobs\InstallApplication;
use App\Jobs\ProvisionServer;
use App\Models\Application;
use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\SourceProvider;
use App\Models\Task;
use App\Models\User;
use App\ShellResult;
use Facades\App\ShellProcessRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('servers page', function () {
    it('allows authenticated users to access the servers page', function () {
        /** @var User */
        $user = User::factory()->create();

        actingAs($user)->get('/servers')->assertOk();
    });

    it('redirects unauthenticated users to the login page when trying to access the applications page', function () {
        get('/servers')->assertRedirect('/login');
    });

    it('fetches only the servers associated with the authenticated user', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $serverA = Server::factory()->for($user)->create();
        $serverB = Server::factory()->for($otherUser)->create();
        $serverC = Server::factory()->for($user)->create();

        $component = Volt::actingAs($user)->test('pages.servers');

        expect($component->servers)->toHaveCount(2);
        expect($component->servers->pluck('id'))
            ->toContain($serverA->id)
            ->toContain($serverC->id)
            ->not->toContain($serverB->id);
    });
});

describe('server provisioning', function () {
    it('can create the server to provision', function () {
        Queue::fake();

        $user = User::factory()->create();
        $serverProvider = ServerProvider::factory()->for($user)->create();

        $component = Volt::actingAs($user)->test('pages.servers.provision')
            ->set('name', 'my-server')
            ->set('server_provider_id', $serverProvider->id)
            ->set('region', 'nyc1')
            ->set('size', 's-1vcpu-512mb-10gb')
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
            ->set('region', 'nyc1')
            ->set('size', 's-1vcpu-512mb-10gb')
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
            ->set('region', 'nyc1')
            ->set('size', 'invalid-size')
            ->call('provision')
            ->assertHasErrors(['size']);
    });
});

describe('create application', function () {
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
            ->name->toBe('Deploying application')
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
            expect($task)->name->toBe('Deploying application');

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
            expect($task)->name->toBe('Deploying application');

            $task->finish(exitCode: 999);
        });

        expect($server->applications()->first()->deployments->last())
            ->status->toBe('failed');
    });
});

describe('applications', function () {
    it('allows authenticated users to access the applications page', function () {
        /** @var User */
        $user = User::factory()->create();

        actingAs($user)->get('/applications')->assertOk();
    });

    it('redirects unauthenticated users to the login page when trying to access the applications page', function () {
        get('/applications')->assertRedirect('/login');
    });

    it('fetches only the applications associated with the authenticated user', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $applicationA = Application::factory()->for(Server::factory()->for($user))->create();
        $applicationB = Application::factory()->for(Server::factory()->for($otherUser))->create();
        $applicationC = Application::factory()->for(Server::factory()->for($user))->create();

        $component = Volt::actingAs($user)->test('pages.applications');

        expect($component->applications)->toHaveCount(2);
        expect($component->applications->pluck('id'))
            ->toContain($applicationA->id)
            ->toContain($applicationC->id)
            ->not->toContain($applicationB->id);
    });
});
