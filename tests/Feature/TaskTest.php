<?php

use App\Callbacks\MarkServerProvisioned;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Support\Facades\Process;

it('runs the task script on the remote server and returns the output', function () {
    Process::fake([
        // Match the actual command that will be run (bash {remoteScriptPath()})
        '*' => Process::result(output: "expected-output\n"),
    ]);

    $server = Server::factory()->create(['ip_address' => '203.0.113.10']);
    $task = Task::factory()->for($server)->create([
        'user' => 'root',
        'script' => 'echo hello',
    ]);

    $task = $task->run();

    expect($task->output)->toBe('expected-output');

    Process::assertRan(function ($process) use ($server, $task) {
        return str_contains($process->command, $server->ip_address)
            && str_contains($process->command, $task->remoteScriptPath());
    });
});

it('runs the correct process commands and updates status when provisioning a task', function () {
    Process::fake();

    $server = Server::factory()->create(['ip_address' => '192.0.2.1']);
    $task = Task::factory()->for($server)->create([
        'name' => 'provision',
        'user' => 'root',
        'script' => 'provision.sh',
        'after_actions' => [
            (new MarkServerProvisioned)->toCallbackArray(),
        ],
        'status' => 'pending',
    ]);

    $task->provision();

    $task->refresh();
    expect($task->status)->toBe('running');

    // Assert the callback is present in the script
    expect($task->script)->toContain("task/{$task->id}/callback");

    // Assert the expected shell command was run
    Process::assertRan(function ($process) use ($task, $server) {
        return str_contains(
                $process->command,
                "ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i "
            ) &&
            str_contains($process->command, "{$task->user}@{$server->ip_address} 'bash -s' <<") &&
            str_contains($process->command, "mkdir -p {$task->fuseDirectory()}");
    });

    // Assert the script upload process was run
    Process::assertRan(function ($process) use ($task, $server) {
        return str_contains($process->command, "scp") &&
            str_contains($process->command, $task->user . '@' . $server->ip_address) &&
            str_contains($process->command, $task->remoteScriptPath());
    });

    // Assert the script execution process was run
    Process::assertRan(function ($process) use ($task, $server) {
        return str_contains($process->command, "ssh") &&
            str_contains($process->command, $task->user) &&
            str_contains($process->command, $server->ip_address) &&
            str_contains($process->command, $task->remoteScriptPath());
    });
});
