<?php

use App\Callbacks\CheckProvisioning;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('marks the server as provisioned when task is succesful', function () {
    $task = Task::factory()->for(Server::factory()->provisioning())->create([
        'exit_code' => 0,
    ]);
    $callback = new CheckProvisioning;
    expect($task->server->isProvisioned())->toBeFalse();

    $callback->handle($task);

    expect($task->server->isProvisioned())->toBeTrue();
});

it('marks the server as failed when task is not succesful', function () {
    $task = Task::factory()->for(Server::factory()->provisioning())->create([
        'exit_code' => 999,
    ]);
    $callback = new CheckProvisioning;
    expect($task->server->isProvisioned())->toBeFalse();

    $callback->handle($task);

    expect($task->server->status)->toBe('failed');
});
