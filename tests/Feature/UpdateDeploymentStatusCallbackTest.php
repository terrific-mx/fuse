<?php

use App\Callbacks\UpdateDeploymentStatus;
use App\Jobs\InstallCaddyFileJob;
use App\Models\Deployment;
use App\Models\Site;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;

it('dispatches a job to install the Caddy file if it has not been installed before', function () {
    Queue::fake();

    // Create a site and deployment where the Caddy file has not been installed
    $site = Site::factory()->create([
        'caddy_installed_at' => null,
    ]);

    $deployment = Deployment::factory()->create([
        'site_id' => $site->id,
    ]);

    $task = Task::factory()->create();

    $callback = new UpdateDeploymentStatus($deployment->id);
    $callback($task);

    Queue::assertPushed(InstallCaddyFileJob::class, function ($job) use ($site) {
        return $job->site->is($site);
    });
});
