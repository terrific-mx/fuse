<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;

it("marks server as provisioning and creates a 'provision' task with the correct user, script, and running status when job runs", function () {
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();
    $server->refresh();
    expect($server->status)->toBe('provisioning');

    $server->refresh();
    expect($server->tasks)->toHaveCount(1);

    $task = $server->tasks()->first();
    expect($task->name)->toBe('provision');
    expect($task->user)->toBe('root');
    expect($task->script)->toBe('provision.sh');
    expect($task->status)->toBe('running');
    expect($task->callback)->toBe(App\Callbacks\MarkServerProvisioned::class);
});
