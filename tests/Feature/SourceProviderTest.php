<?php

use App\Models\Organization;
use App\Models\SourceProvider;
use App\Models\User;
use Livewire\Volt\Volt;
use App\Services\GitHubClient;
use PHPUnit\TextUI\Configuration\Source;

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

it('validates the GitHub token is valid when storing a source provider', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $component = Volt::actingAs($user)->test('source-providers.index')
        ->set('form.name', 'Invalid Token Provider')
        ->set('form.type', 'GitHub')
        ->set('form.meta', ['token' => 'invalid-token'])
        ->call('save');

    $component->assertHasErrors();
    expect($user->currentOrganization->sourceProviders)->toHaveCount(0);
});

it('can delete a source provider for the current user organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $provider = SourceProvider::factory()->for($user->currentOrganization)->create();

    $component = Volt::actingAs($user)->test('source-providers.index')
        ->call('delete', $provider->id);

    expect($user->currentOrganization->sourceProviders()->find($provider->id))->toBeNull();
});

it('cannot delete a source provider outside the current user organization', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $otherOrg = Organization::factory()->create();
    $provider = SourceProvider::factory()->for($otherOrg)->create();

    $component = Volt::actingAs($user)->test('source-providers.index')
        ->call('delete', $provider->id);

    $component->assertForbidden();
    expect($otherOrg->sourceProviders()->find($provider->id))->not->toBeNull();
});

it('cannot delete a source provider if it is used by an existing site')
    ->todo('Implement: Prevent deletion if a site uses the source provider');
