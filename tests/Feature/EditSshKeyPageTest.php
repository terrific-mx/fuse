<?php

use App\Jobs\AuthorizeSshKeyOnServerJob;
use App\Jobs\DeauthorizeSshKeyOnServerJob;
use App\Models\Organization;
use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

test('assigning servers to ssh key dispatches authorize jobs', function () {
    Queue::fake();

    $organization = Organization::factory()->create();
    $sshKey = SshKey::factory()->for($organization)->create();
    $servers = Server::factory()->count(3)->for($organization)->create();

    actingAs($organization->user);

    $assigned = $servers->take(2);
    $unassigned = $servers->last();

    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('selectedServers', $assigned->pluck('id')->toArray())
        ->call('assignServers')
        ->assertHasNoErrors();

    $sshKey->refresh();

    $assigned->each(function ($server) use ($sshKey) {
        expect($sshKey->servers->contains($server))->toBeTrue();
    });
    expect($sshKey->servers->contains($unassigned))->toBeFalse();

    $assigned->each(function ($server) use ($sshKey) {
        Queue::assertPushed(AuthorizeSshKeyOnServerJob::class, function ($job) use ($sshKey, $server) {
            return $job->sshKey->is($sshKey) && $job->server->is($server);
        });
    });
});

test('deleting an ssh key removes it from all servers and dispatches deauthorize jobs', function () {
    Queue::fake();

    $organization = Organization::factory()->create();
    $sshKey = SshKey::factory()->for($organization)->create();
    $servers = Server::factory()->count(3)->for($organization)->create();

    actingAs($organization->user);

    // Assign all servers first
    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('selectedServers', $servers->pluck('id')->toArray())
        ->call('assignServers')
        ->assertHasNoErrors();
    $sshKey->refresh();

    // Delete the SSH key
    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->call('deleteSshKey')
        ->assertHasNoErrors();

    // Assert the SSH key is deleted
    expect($sshKey->refresh())->toBeNull();

    // Assert the SSH key is removed from all servers
    $servers->each(function ($server) use ($sshKey) {
        expect($server->sshKeys()->where('ssh_key_id', $sshKey->id)->exists())->toBeFalse();
    });

    // Assert deauthorize jobs dispatched for all servers
    $servers->each(function ($server) use ($sshKey) {
        Queue::assertPushed(DeauthorizeSshKeyOnServerJob::class, function ($job) use ($sshKey, $server) {
            return $job->sshKey->is($sshKey) && $job->server->is($server);
        });
    });
});

test('unassigning servers from ssh key dispatches deauthorize jobs', function () {
    Queue::fake();

    $organization = Organization::factory()->create();
    $sshKey = SshKey::factory()->for($organization)->create();
    $servers = Server::factory()->count(3)->for($organization)->create();

    actingAs($organization->user);

    // Assign all servers first
    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('selectedServers', $servers->pluck('id')->toArray())
        ->call('assignServers')
        ->assertHasNoErrors();
    $sshKey->refresh();

    // Now unassign one server
    $stillAssigned = $servers->first();
    $toUnassign = $servers->last();
    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('selectedServers', [$stillAssigned->id, $servers[1]->id])
        ->call('assignServers')
        ->assertHasNoErrors();
    $sshKey->refresh();

    expect($sshKey->servers->contains($stillAssigned))->toBeTrue();
    expect($sshKey->servers->contains($toUnassign))->toBeFalse();

    Queue::assertPushed(DeauthorizeSshKeyOnServerJob::class, function ($job) use ($sshKey, $toUnassign) {
        return $job->sshKey->is($sshKey) && $job->server->is($toUnassign);
    });
});
