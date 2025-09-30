<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\PendingProcess;
use Illuminate\Contracts\Process\ProcessResult;

it("marks server as provisioning, creates a 'provision' task, and runs a shell process to ensure working directory exists when job runs", function () {
    Process::fake();
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();

    // Assert the expected shell command was run
    $server->refresh();
    $task = $server->tasks()->first();
    Process::assertRan(function (PendingProcess $process, ProcessResult $result) use ($server, $task) {
        return $process->command === "ssh {$task->user}@{$server->ip_address} 'bash -s' <<TOKEN mkdir -p /var/www TOKEN";
    });

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
