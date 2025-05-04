<?php

use App\Models\Server;
use App\Scripts\Script;
use App\ShellProcessRunner;
use App\ShellResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;

uses(RefreshDatabase::class);

it('retrieves the server public address when checking provisioning readiness if address is not already set', function () {
    ShellProcessRunner::mock([
        new ShellResult(exitCode: 1, output: '', timedOut: true), // GetCurrentDirectory Script Result
    ]);

    $server = Server::factory()->create();

    $this->assertNull($server->public_address);

    $server->isReadyForProvisioning();

    $this->assertNotNull($server->public_address);
});

it('determines server is ready for provisioning when we can access the server and all required conditions are met', function () {
    $server = Server::factory()->create();

    ShellProcessRunner::mock([
        new ShellResult(0, '/root'), // GetCurrentDirectory Script Result
    ]);

    expect($server->isReadyForProvisioning())->toBeTrue();
});

it('determines server is not ready for provisioning when server has no IP address assigned', function () {
    $server = Server::factory()->create(['name' => 'server-that-does-not-return-an-ip-address']);

    expect($server->isReadyForProvisioning())->toBeFalse();
});

it('determines server is not ready for provisioning when we cannot get root access via pwd command', function () {
    ShellProcessRunner::mock([
        new ShellResult(exitCode: 0, output: 'non-root'), // GetCurrentDirectory Script Result
    ]);

    $server = Server::factory()->create();

    expect($server->isReadyForProvisioning())->toBeFalse();
});

it('creates a task when running a script on a server', function () {
    Process::fake();

    $server = Server::factory()->create();
    $script = new Script;

    $server->run($script);

    expect($server->tasks)->toHaveCount(1);
    expect($server->tasks()->first())
        ->name->toBe($script->name())
        ->user->toBe($script->sshAs)
        ->options->toBe(['timeout' => $script->timeout()])
        ->script->toBe((string) $script)
        ->output->toBe('');
});

it('successfully executes the provisioning script and creates a corresponding task', function () {
    Process::fake();

    $server = Server::factory()->withPublicAddress()->create();

    $server->runProvisioningScript();

    expect($server->tasks()->first()->name)->toBe('Provisioning Server');
});

it('prevents duplicate provisioning by not running the script when server is already provisioning', function () {
    $server = Server::factory()->withPublicAddress()->provisioning()->create();

    $server->runProvisioningScript();

    expect($server->tasks)->toHaveCount(0);
});

it('sets the server provisioning status flag to true when executing the provisioning script', function () {
    Process::fake();

    $server = Server::factory()->withPublicAddress()->create();

    $server->runProvisioningScript();

    expect($server->isProvisioning())->toBeTrue();
});

it('creates a task when running a script in background on a server', function () {
    Process::fake();

    $server = Server::factory()->create();
    $script = new Script;

    $server->runInBackground($script);

    expect($server->tasks)->toHaveCount(1);
    expect($server->tasks()->first())
        ->name->toBe($script->name())
        ->user->toBe($script->sshAs)
        ->options->toBe(['timeout' => $script->timeout()])
        ->output->toBe('');
});
