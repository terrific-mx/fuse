<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\ServerProvider;
use App\Models\Server;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('users can create a server for their current organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $provider = ServerProvider::factory()->for($user->currentOrganization)->create();

    $component = Volt::actingAs($user)
        ->test('servers')
        ->set('name', 'Test Server')
        ->set('provider_id', $provider->id)
        ->set('region', 'eu-central')
        ->set('type', 'cx21')
        ->call('save');

    $component->assertHasNoErrors();

    expect($user->currentOrganization->servers)->toHaveCount(1);

    $server = $user->currentOrganization->servers()->first();

    expect($server->name)->toBe('Test Server');
    expect($server->provider->is($provider))->toBeTrue();
    expect($server->region)->toBe('eu-central');
    expect($server->type)->toBe('cx21');
});
