<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    Queue::fake();
    Log::spy();
    Process::fake();
});

it('dispatches readiness job after server creation', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $server = Server::factory()->for($user->currentOrganization)->create([
        'status' => 'initializing',
    ]);

    // Simulate server creation event
    // ...

    // Assert job dispatched
    Queue::assertPushed(ServerReadinessJob::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});

it('marks server as ready if SSH succeeds', function () {
    $server = Server::factory()->create(['status' => 'initializing']);

    // Fake SSH process to succeed
    Process::fake([
        'ssh*' => Process::result(
            output: '',
            errorOutput: '',
            exitCode: 0,
        ),
    ]);

    // Run job
    (new ServerReadinessJob($server))->handle();

    // Assert SSH was attempted
    Process::assertRan('ssh*');

    $server->refresh();
    expect($server->status)->toBe('ready');

});

it('retries job if SSH fails, up to max attempts', function () {
    $server = Server::factory()->create(['status' => 'initializing']);

    // Fake SSH process to fail
    Process::fake([
        'ssh*' => Process::result(
            output: '',
            errorOutput: 'Connection refused',
            exitCode: 1,
        ),
    ]);

    // Run job
    $job = new ServerReadinessJob($server);
    $job->attempts = 1;
    $job->maxAttempts = 3;
    $job->handle();

    // Assert SSH was attempted
    Process::assertRan('ssh*');

    // Should re-dispatch itself
    Queue::assertPushed(ServerReadinessJob::class);
    expect($server->status)->toBe('initializing');
});

it('marks server as failed if max attempts reached', function () {
    $server = Server::factory()->create(['status' => 'initializing']);

    // Fake SSH process to fail
    Process::fake([
        'ssh*' => Process::result(
            output: '',
            errorOutput: 'Connection refused',
            exitCode: 1,
        ),
    ]);

    // Run job at max attempts
    $job = new ServerReadinessJob($server);
    $job->attempts = 3;
    $job->maxAttempts = 3;
    $job->handle();

    // Assert SSH was attempted
    Process::assertRan('ssh*');

    $server->refresh();
    expect($server->status)->toBe('failed');
    Notification::assertSentTo($server->user, ServerFailedNotification::class);
});


