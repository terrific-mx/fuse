<?php

use App\Jobs\ProvisionServer;
use App\Models\SshKey;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

it('creates a server for the user\'s current organization', function () {
    Queue::fake();
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)
        ->test('servers.create')
        ->set('name', 'Test Server')
        ->set('ip_address', '192.0.2.1')
        ->set('memory', 2048)
        ->call('save');

    $component->assertHasNoErrors();

    expect($user->currentOrganization->servers)->toHaveCount(1);

    $server = $user->currentOrganization->servers()->first();

    expect($server->name)->toBe('Test Server');
    expect($server->ip_address)->toBe('192.0.2.1');
    expect($server->memory)->toBe(2048);
    expect($server->sudo_password)->not->toBeEmpty();
    expect($server->database_password)->not->toBeEmpty();

    Queue::assertPushed(ProvisionServer::class, function ($job) use ($server) {
        expect($job->server->is($server))->toBeTrue();

        return true;
    });
});

it('can associate ssh keys with a server via the servers.create component', function () {
    Queue::fake();
    $user = User::factory()->withPersonalOrganization()->create();
    $organization = $user->currentOrganization;
    $sshKey1 = SshKey::factory()->for($organization)->create();
    $sshKey2 = SshKey::factory()->for($organization)->create();

    $component = Volt::actingAs($user)
        ->test('servers.create')
        ->set('name', 'Server With Keys')
        ->set('ip_address', '203.0.113.10')
        ->set('memory', 2048)
        ->set('ssh_keys', [$sshKey1->id, $sshKey2->id])
        ->call('save');

    $component->assertHasNoErrors();

    $server = $organization->servers()->latest()->first();

    expect($server->sshKeys)->toHaveCount(2);
    expect($server->sshKeys->pluck('id'))->toContain($sshKey1->id);
    expect($server->sshKeys->pluck('id'))->toContain($sshKey2->id);
});
