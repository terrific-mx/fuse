<?php

use App\Callbacks\UpdateDeploymentStatus;
use App\Jobs\InstallCaddyFileJob;
use App\Models\Deployment;
use App\Models\Site;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;

it('dispatches a job to install the Caddy file if it has not been installed before', function () {
    Queue::fake();

    $site = Site::factory()->caddyNotInstalled()->create();
    $deployment = Deployment::factory()->for($site)->create();
    $task = Task::factory()->create();
    $callback = new UpdateDeploymentStatus($deployment->id);

    $callback($task);

    Queue::assertPushed(InstallCaddyFileJob::class, function ($job) use ($site) {
        return $job->site->is($site);
    });
});

it('sets the deployment status to deployed after running the callback', function () {
    Queue::fake();

    $deployment = Deployment::factory()->pending()->create();
    $task = Task::factory()->create();
    $callback = new UpdateDeploymentStatus($deployment->id);

    $callback($task);

    $deployment->refresh();
    expect($deployment->status)->toBe('deployed');
});
