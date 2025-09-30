<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

it('creates a server for the user\'s current organization', function () {
    Queue::fake();
    $user = User::factory()->withPersonalOrganization()->create();
    $provider = ServerProvider::factory()->for($user->currentOrganization)->create();

    $component = Volt::actingAs($user)
        ->test('servers.index')
        ->set('form.name', 'Test Server')
        ->set('form.provider_id', $provider->id)
        ->set('form.region', 'fsn1')
        ->set('form.type', 'cx21')
        ->call('save');

    $component->assertHasNoErrors();

    expect($user->currentOrganization->servers)->toHaveCount(1);

    $server = $user->currentOrganization->servers()->first();

    expect($server->name)->toBe('Test Server');
    expect($server->provider->is($provider))->toBeTrue();
    expect($server->region)->toBe('fsn1');
    expect($server->type)->toBe('cx21');
    expect($server->provider_server_id)->toStartWith('simulated-');
    expect($server->ip_address)->toStartWith('192.0.2.');

    Queue::assertPushed(ProvisionServer::class, function ($job) use ($server) {
        expect($job->server->is($server))->toBeTrue();

        return true;
    });
});

it('marks the server as provisioning', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $server->markProvisioning();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});

it('creates a provision task with correct attributes', function () {
    $server = Server::factory()->create();
    $task = $server->createProvisionTask();

    expect($task)->not->toBeNull();
    expect($task->name)->toBe('provision');
    expect($task->user)->toBe('root');
    expect($task->script)->toBe('provision.sh');
    expect($task->callback)->toBe(App\Callbacks\MarkServerProvisioned::class);
    expect($task->server_id)->toBe($server->id);
});

it('provisions the server: marks provisioning, creates task, and calls task provision', function () {
    Process::fake();
    $server = Server::factory()->create(['status' => 'pending']);

    $server->provision();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
    $task = $server->tasks()->first();
    expect($task)->not->toBeNull();
    expect($task->name)->toBe('provision');
    expect($task->status)->toBe('running');
    // Optionally, assert process calls here or in TaskTest
});
