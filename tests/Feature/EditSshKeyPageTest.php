<?php

use App\Models\Organization;
use App\Models\Server;
use App\Models\SshKey;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

test('user can assign ssh key to servers from volt edit page', function () {
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
});
