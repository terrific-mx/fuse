<?php

use App\Jobs\UpdateServerPublicKey;
use App\Models\Server;
use App\ShellResult;
use Facades\App\ShellProcessRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates the server public key', function () {
    ShellProcessRunner::mock([new ShellResult(exitCode: 0, output: 'public-ssh-key')]);
    $server = Server::factory()->provisioning()->create();
    $job = new UpdateServerPublicKey($server);
    expect($server->public_key)->toBeNull();

    $job->handle();

    expect($server->fresh()->public_key)->toBe('public-ssh-key');
});
