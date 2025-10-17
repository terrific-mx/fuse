<?php

use App\Jobs\DeauthorizeSshKeyOnServerJob;
use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Support\Facades\Process;

it('creates and runs a deauthorize_ssh_key task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Remove public key
            ->push(Process::result(output: 'key deauthorized')), // Execute deauthorization
    ]);

    $server = Server::factory()->create();
    $sshKey = SshKey::factory()->for($server->organization)->create();
    $sshKey->servers()->attach($server->id);

    $job = new DeauthorizeSshKeyOnServerJob($sshKey, $server);
    $job->handle();

    $task = $server->tasks()
        ->where('name', 'deauthorize_ssh_key')
        ->where('user', 'fuse')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain('sed -i.bak');
    expect($task->script)->toContain('~/.ssh/authorized_keys');

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('removes the association if the job fails', function () {
    $server = Server::factory()->create();
    $sshKey = SshKey::factory()->for($server->organization)->create();
    $sshKey->servers()->attach($server->id);
    expect($sshKey->servers()->where('id', $server->id)->exists())->toBeTrue();

    $job = new DeauthorizeSshKeyOnServerJob($sshKey, $server);
    $job->failed(new Exception('Simulated failure'));

    expect($sshKey->servers()->where('id', $server->id)->exists())->toBeFalse();
});

it('fails the job if the deauthorization task fails', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Remove public key
            ->push(Process::result(exitCode: 1, output: 'failed')), // Deauthorization fails
    ]);

    $server = Server::factory()->create();
    $sshKey = SshKey::factory()->for($server->organization)->create();
    $sshKey->servers()->attach($server->id);

    $job = new DeauthorizeSshKeyOnServerJob($sshKey, $server);

    expect(fn () => $job->handle())->toThrow(Exception::class);
});
