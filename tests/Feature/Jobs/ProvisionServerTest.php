<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\ShellProcessRunner;
use App\ShellResult;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('delete the job if server has been provisioned', function () {
    $server = Server::factory()->provisioned()->create();
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertDeleted();
});

it('fails the job if the server has been created after 15 minutes', function () {
    $server = Server::factory()->provisioning()->create(['created_at' => now()->subMinutes(16)]);

    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertFailed();
});

it('release de job if server is still being provisioned', function () {
    $server = Server::factory()->provisioning()->create();

    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(delay: 30);
});

it('runs the provisioning script if server is ready for provisioning', function () {
    ShellProcessRunner::mock([
        new ShellResult(0, '/root'), // GetCurrentDirectory Script Result
        new ShellResult(0, ''), // GetAptLockStatus Script Result
    ]);

    $server = Server::factory()->withPublicAddress()->creating()->create();

    expect($server->isReadyForProvisioning())->toBeTrue();

    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(delay: 30);
});

it('it releases the job when we are not ready for connect', function () {
    ShellProcessRunner::mock([
        new ShellResult(exitCode: 1, output: '', timedOut: true), // GetCurrentDirectory Script Result
    ]);

    $server = Server::factory()->creating()->create();
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(delay: 30);
});
