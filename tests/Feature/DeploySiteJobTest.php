<?php

use App\Jobs\DeploySite;
use App\Models\Deployment;
use App\Models\Task;
use Illuminate\Support\Facades\Bus;

it('creates a server task to deploy the site', function () {
    $deployment = Deployment::factory()->create();
    $server = $deployment->site->server;

    (new DeploySite($deployment))->handle();

    expect($server->tasks)->toHaveCount(1);

    $task = $server->tasks->first();
    expect($task)->not->toBeNull();
    expect($task->status)->toBe('pending');
    expect($task->name)->toBe('deploy');
});
