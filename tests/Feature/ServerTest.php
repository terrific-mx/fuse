<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Models\SshKey;
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
    expect($server->sudo_password)->not->toBeEmpty();
    expect($server->database_password)->not->toBeEmpty();

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

it('can associate ssh keys with a server via the servers.index component', function () {
    Queue::fake();
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    $sshKey1 = SshKey::factory()->for($organization)->create();
    $sshKey2 = SshKey::factory()->for($organization)->create();

    $component = Volt::actingAs($user)
        ->test('servers.index')
        ->set('form.name', 'Server With Keys')
        ->set('form.ip_address', '203.0.113.10')
        ->set('form.ssh_keys', [$sshKey1->id, $sshKey2->id])
        ->call('save');

    $component->assertHasNoErrors();

    $server = $organization->servers()->latest()->first();

    expect($server->sshKeys)->toHaveCount(2);
    expect($server->sshKeys->pluck('id'))->toContain($sshKey1->id);
    expect($server->sshKeys->pluck('id'))->toContain($sshKey2->id);
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
