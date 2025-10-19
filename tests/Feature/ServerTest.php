<?php

use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('marks the server as provisioning', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $server->markProvisioning();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});

it('creates a provision task with correct attributes', function () {
    $server = Server::factory()->create();
    $task = $server->createProvisionTask();

    expect($task)->not->toBeNull();
    expect($task->name)->toBe('provision');
    expect($task->user)->toBe('root');
    expect($task->after_actions)->toContain((new App\Callbacks\MarkServerProvisioned)->toCallbackArray());
    expect($task->server_id)->toBe($server->id);
});

it('provisions the server: marks provisioning, creates task, and calls task provision', function () {
    Process::fake();
    $server = Server::factory()->create(['status' => 'pending']);

    $server->provision();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
    $task = $server->tasks()->first();
    expect($task)->not->toBeNull();
    expect($task->name)->toBe('provision');
    expect($task->status)->toBe('running');
    // Optionally, assert process calls here or in TaskTest
});
