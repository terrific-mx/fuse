<?php

use App\Models\Site;
use Livewire\Volt\Volt;

it('dispatches a new deployment for a site', function () {
    $site = Site::factory()->create();

    $component = Volt::actingAs($site->server->organization->user)
        ->test('servers.sites.deployments', ['server' => $site->server, 'site' => $site])
        ->call('triggerDeployment');

    $component->assertHasNoErrors();

    $site->refresh();
    expect($site->deployments)->toHaveCount(1);

    $deployment = $site->deployments()->first();
    expect($deployment->status)->toBe('pending');
    expect($deployment->triggered_by)->toBe($site->server->organization->user->id);
});
