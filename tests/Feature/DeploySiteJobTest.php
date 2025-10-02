<?php

use App\Jobs\DeploySite;
use App\Models\Deployment;

use Illuminate\Support\Facades\Process;

it('creates a server task to deploy the site and provisions it', function () {
    Process::fake();

    $deployment = Deployment::factory()->create();
    $server = $deployment->site->server;

    (new DeploySite($deployment))->handle();

    expect($server->tasks)->toHaveCount(1);

    $task = $server->tasks->first();
    expect($task)->not->toBeNull();
    expect($task->name)->toBe('deploy');
    expect($task->user)->toBe('fuse');
    expect($task->script)->not->toBeNull();
    expect($task->script)->not->toBe('');

    expect($task->status)->toBe('running');
    Process::assertRan(fn () => true);
});
