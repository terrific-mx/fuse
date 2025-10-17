<?php

use App\Jobs\AuthorizeSshKeyOnServerJob;
use App\Jobs\DeauthorizeSshKeyOnServerJob;
use App\Models\Organization;
use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

test('user can assign and unassign ssh key to servers from volt edit page and jobs are dispatched', function () {
    $organization = Organization::factory()->create();
    $sshKey = SshKey::factory()->for($organization)->create();
    $servers = Server::factory()->count(3)->for($organization)->create();

    actingAs($organization->user);
    Queue::fake();

    // Assign two servers
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

    // Assert jobs dispatched for assigned servers
    $assigned->each(function ($server) use ($sshKey) {
        Queue::assertPushed(AuthorizeSshKeyOnServerJob::class, function ($job) use ($sshKey, $server) {
            return $job->sshKey->is($sshKey) && $job->server->is($server);
        });
    });

    // Now unassign one server (the second assigned one)
    $stillAssigned = $assigned->first();
    $toUnassign = $assigned->last();

    Volt::test('ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('selectedServers', [$stillAssigned->id])
        ->call('assignServers')
        ->assertHasNoErrors();

    $sshKey->refresh();

    expect($sshKey->servers->contains($stillAssigned))->toBeTrue();
    expect($sshKey->servers->contains($toUnassign))->toBeFalse();

    // Assert deauthorize job dispatched for unassigned server
    Queue::assertPushed(DeauthorizeSshKeyOnServerJob::class, function ($job) use ($sshKey, $toUnassign) {
        return $job->sshKey->is($sshKey) && $job->server->is($toUnassign);
    });
});
