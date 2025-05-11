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
