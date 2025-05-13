<?php

use App\Jobs\AddSshKeyToServers;
use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;

uses(RefreshDatabase::class);

it('creates a task to run the script to add the SSH authorized key to the server', function () {
    Process::fake();

    $sshKey = SshKey::factory()->create();
    $server = Server::factory()->create();

    $job = new AddSshKeyToServers($sshKey, $server);

    $job->handle();

    expect($server->tasks)->toHaveCount(1);
    expect($server->tasks->first())->name->toBe('Adding authorized key');
});
