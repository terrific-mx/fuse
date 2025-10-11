<?php

use App\Jobs\RetrieveRemoteSshKey;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs a get_ssh_key task for the server and updates the public_ssh_key', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'ssh-rsa AAAATESTKEY')), // Execute script
    ]);

    $server = Server::factory()->withoutPublicSshKey()->create();
    $job = new RetrieveRemoteSshKey($server);

    $job->handle();

    $sshKeyTask = $server->tasks()
        ->where('name', 'get_ssh_key')
        ->where('user', 'fuse')
        ->first();

    expect($sshKeyTask)->not()->toBeNull();
    expect($sshKeyTask->script)->toContain('cat ~/.ssh/id_rsa.pub');

    Process::assertRan(function ($process, $result) {
        return true;
    });

    $server->refresh();
    expect($server->public_ssh_key)->toBe('ssh-rsa AAAATESTKEY');
});
