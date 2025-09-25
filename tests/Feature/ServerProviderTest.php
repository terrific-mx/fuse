<?php

use App\Models\User;
use App\Services\HetznerCloudClient;
use Livewire\Volt\Volt;

it('can store a server provider for the current user organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)->test('server-providers.index')
        ->set('form.name', 'Test Provider')
        ->set('form.type', 'Hetzner Cloud')
        ->set('form.meta', ['token' => 'test-key'])
        ->call('save');

    $component->assertHasNoErrors();

    expect($user->currentOrganization->serverProviders)->toHaveCount(1);

    $provider = $user->currentOrganization->serverProviders()->first();
    expect($provider->name)->toBe('Test Provider');
    expect($provider->type)->toBe('Hetzner Cloud');
    expect($provider->meta)->toBe(['token' => 'test-key']);
    expect($provider->client())->toBeInstanceOf(HetznerCloudClient::class);
});
