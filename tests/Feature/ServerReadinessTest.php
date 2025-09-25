<?php
use App\Models\Server;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;
use App\Jobs\ServerReadinessJob;
use App\Notifications\ServerFailedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    Queue::fake();
});

it('marks server as ready if SSH succeeds', function () {
    $server = Server::factory()->create(['status' => 'initializing']);
    Process::fake([
        "ssh -o BatchMode=yes -o ConnectTimeout=5 root@{$server->ip_address} exit" => Process::result(
            output: '',
            errorOutput: '',
            exitCode: 0,
        ),
    ]);

    (new ServerReadinessJob($server))->handle();

    Process::assertRan("ssh -o BatchMode=yes -o ConnectTimeout=5 root@{$server->ip_address} exit");
    $server->refresh();
    expect($server->status)->toBe('ready');
});

it('does not mark server as ready if SSH fails', function () {
    $server = Server::factory()->create(['status' => 'initializing']);
    Process::fake([
        '*' => Process::result(
            output: '',
            errorOutput: 'Connection refused',
            exitCode: 255,
        ),
    ]);

    (new ServerReadinessJob($server))->handle();

    Process::assertRan("ssh -o BatchMode=yes -o ConnectTimeout=5 root@{$server->ip_address} exit");
    $server->refresh();
    expect($server->status)->toBe('initializing');
});

it('marks server as failed and notifies user if job fails after max attempts', function () {
    $server = Server::factory()->create(['status' => 'initializing']);
    $job = new ServerReadinessJob($server);

    $job->failed();

    $server->refresh();
    expect($server->status)->toBe('failed');
    Notification::assertSentTo(
        [$server->organization->user],
        ServerFailedNotification::class
    );
});
