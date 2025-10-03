<?php

use App\Jobs\DeploySite;
use App\Models\Site;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

it('dispatches a new deployment for a site', function () {
    Queue::fake();
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

    Queue::assertPushed(DeploySite::class, function ($job) use ($deployment) {
        return $job->deployment->is($deployment);
    });
});
