<?php

use App\Callbacks\CheckProvisioning;
use App\Jobs\UpdateServerPublicKey;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates the server status to provisioned when the task exits successfully', function () {
    Queue::fake();
    $task = Task::factory()->for(Server::factory()->provisioning())->successful()->create();
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

it('dispatches a job to update the server public key when task exist successfully', function () {
    Queue::fake();
    $server = Server::factory()->provisioning()->create();
    $task = Task::factory()->for($server)->successful()->create();
    $callback = new CheckProvisioning;

    $callback->handle($task);

    Queue::assertPushed(UpdateServerPublicKey::class);
});
