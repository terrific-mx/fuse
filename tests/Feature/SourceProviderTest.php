<?php

use App\Models\User;
use Livewire\Volt\Volt;
use App\Services\GitHubClient;

it('can store a GitHub source provider for the current user organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)->test('source-providers.index')
        ->set('form.name', 'My GitHub')
        ->set('form.type', 'GitHub')
        ->set('form.meta', ['token' => 'ghp_testtoken'])
        ->call('save');

    $component->assertHasNoErrors();
    expect($user->currentOrganization->sourceProviders)->toHaveCount(1);

    $provider = $user->currentOrganization->sourceProviders()->first();
    expect($provider->name)->toBe('My GitHub');
    expect($provider->type)->toBe('GitHub');
    expect($provider->meta)->toBe(['token' => 'ghp_testtoken']);
    expect($provider->client())->toBeInstanceOf(GitHubClient::class);
});
