<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

it('creates a server for the user\'s current organization', function () {
    Queue::fake();
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)
        ->test('servers.index')
        ->set('form.name', 'Test Server')
        ->set('form.ip_address', '192.0.2.1')
        ->call('save');

    $component->assertHasNoErrors();

    expect($user->currentOrganization->servers)->toHaveCount(1);

    $server = $user->currentOrganization->servers()->first();

    expect($server->name)->toBe('Test Server');
    expect($server->ip_address)->toBe('192.0.2.1');

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
