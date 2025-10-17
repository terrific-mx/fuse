<?php

use App\Jobs\AuthorizeSshKeyOnServerJob;
use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Support\Facades\Process;

it('creates and runs an authorize_ssh_key task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload public key
            ->push(Process::result(output: 'key authorized')), // Execute authorization
    ]);

    $server = Server::factory()->create();
    $sshKey = SshKey::factory()->create([
        'organization_id' => $server->organization_id,
    ]);

    $job = new AuthorizeSshKeyOnServerJob($sshKey, $server);
    $job->handle();

    $task = $server->tasks()
        ->where('name', 'authorize_ssh_key')
        ->where('user', 'fuse')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain($sshKey->public_key);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});
