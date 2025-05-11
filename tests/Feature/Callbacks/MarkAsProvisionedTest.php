<?php

use App\Callbacks\CheckProvisioning;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('marks the server as provisioned', function () {
    $task = Task::factory()->for(Server::factory()->provisioning())->create();
    $callback = new CheckProvisioning;
    expect($task->server->isProvisioned())->toBeFalse();

    $callback->handle($task);

    expect($task->server->isProvisioned())->toBeTrue();
});
