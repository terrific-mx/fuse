<?php

use App\Callbacks\UpdateDeploymentStatus;
use App\Jobs\DeploySite;
use App\Models\Deployment;

use Illuminate\Support\Facades\Process;

it('releases the job for 30 seconds if the deployment is still deploying', function () {
    $deployment = Deployment::factory()->deploying()->create();
    $job = (new DeploySite($deployment))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
});

it('fails the job if the deployment is older than 10 minutes', function () {
    $deployment = Deployment::factory()->create([
        'created_at' => now()->subMinutes(11),
    ]);
    $job = (new DeploySite($deployment))->withFakeQueueInteractions();

    $job->handle();

    $job->assertFailed();

    $job->failed();

    $deployment->refresh();
    expect($deployment->status)->toBe('failed');
});

it('deletes the job if the deployment is already deployed', function () {
    $deployment = Deployment::factory()->deployed()->create();
    $job = (new DeploySite($deployment))->withFakeQueueInteractions();

    $job->handle();

    $job->assertDeleted();
});

it('creates a server task to deploy the site', function () {
    Process::fake();

    $deployment = Deployment::factory()->pending()->create();
    $server = $deployment->site->server;

    $job = (new DeploySite($deployment))->withFakeQueueInteractions();
    $job->handle();

    expect($server->tasks)->toHaveCount(1);

    $task = $server->tasks->first();
    expect($task)->not->toBeNull();
    expect($task->name)->toBe('deploy');
    expect($task->user)->toBe('fuse');
    expect($task->script)->not->toBeNull();
    expect($task->script)->not->toBe('');
    expect($task->after_actions)->toContain([
        'class' => UpdateDeploymentStatus::class,
        'args' => ['deployment_id' => $deployment->id],
    ]);

    expect($task->status)->toBe('running');
    Process::assertRan(fn () => true);

    $job->assertReleased(30);
});
