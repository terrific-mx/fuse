<?php

use App\Models\User;
use App\Models\ServerProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

use Illuminate\Support\Facades\Queue;
use App\Jobs\ProvisionServer;

test('users can create a server for their current organization', function () {
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
    expect($server->provider_server_id)->toStartWith('hetzner-');

    Queue::assertPushed(ProvisionServer::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});
