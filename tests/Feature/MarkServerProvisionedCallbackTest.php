<?php

use App\Models\Server;
use App\Callbacks\MarkServerProvisioned;
use App\Models\Task;

it('marks a server as provisioned', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $task = Task::factory()->for($server)->create(['status' => 'pending']);
    $callback = new MarkServerProvisioned();

    $callback($task);

    expect($server->fresh()->status)->toBe('provisioned');
});
