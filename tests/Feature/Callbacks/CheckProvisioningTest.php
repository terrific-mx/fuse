<?php

use App\Callbacks\CheckProvisioning;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates the server status to provisioned when the task exits successfully', function () {
    $task = Task::factory()->for(Server::factory()->provisioning())->create([
        'exit_code' => 0,
    ]);
    $callback = new CheckProvisioning;
    expect($task->server->isProvisioned())->toBeFalse();

    $callback->handle($task);

    expect($task->server->isProvisioned())->toBeTrue();
});

it('updates the server status to failed when the task exits with a non-zero exit code', function () {
    $task = Task::factory()->for(Server::factory()->provisioning())->create([
        'exit_code' => 999,
    ]);
    $callback = new CheckProvisioning;
    expect($task->server->isProvisioned())->toBeFalse();

    $callback->handle($task);

    expect($task->server->status)->toBe('failed');
});
