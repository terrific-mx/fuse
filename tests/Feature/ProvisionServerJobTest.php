<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Notifications\ServerProvisioningFailed;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Process;

it('calls provision on the server when the job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    $mock = Mockery::mock($server);
    $mock->shouldReceive('isReadyForProvisioning')->andReturn(true);
    $mock->shouldReceive('provision')->once()->andReturnNull();

    (new ProvisionServer($mock))->handle();
});

it('deletes the job if the server is already provisioned', function () {
    $server = Server::factory()->create(['status' => 'provisioned']);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertDeleted();
});

it('notifies the user who created the server when the server is already provisioned', function () {
    Notification::fake();
    $server = Server::factory()->provisioned()->create();
    $job = new ProvisionServer($server);

    $job->handle();

    Notification::assertSentTo(
        $server->createdBy,
        \App\Notifications\ServerProvisioned::class,
        function ($notification, $channels) use ($server) {
            return $notification->server->is($server);
        }
    );
});

it('fails the job if the server is older than 15 minutes', function () {
    $server = Server::factory()->create([
        'status' => 'pending',
        'created_at' => Carbon::now()->subMinutes(16),
    ]);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertFailed();
});

it('releases the job for 30 seconds if the server is still provisioning', function () {
    $server = Server::factory()->create([
        'status' => 'provisioning',
    ]);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
});

it('deletes the server when the job fails', function () {
    $server = Server::factory()->create();
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->failed(new Exception('Simulated failure'));

    expect($server->fresh())->toBeNull();
});

it('notifies the user who created the server when provisioning fails', function () {
    Notification::fake();
    $server = Server::factory()->create();
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->failed(new Exception('Simulated failure'));

    Notification::assertSentTo(
        $server->createdBy,
        ServerProvisioningFailed::class,
        function ($notification, $channels) use ($server) {
            return $notification->server->is($server);
        }
    );
});

it('does not provision the server if it is not ready for provisioning', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $mock = Mockery::mock($server);
    $mock->shouldReceive('isReadyForProvisioning')->andReturn(false);
    $mock->shouldReceive('provision')->never();
    $job = (new ProvisionServer($mock))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
});

it('considers the server ready if the working directory is /root and no apt lock is present', function () {
    Process::fake([
        '*' => Process::sequence()
            // pwd task
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: '/root')) // Execute script

            // apt lock status task
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: '', exitCode: 1)), // Execute script (empty output, success)
    ]);

    $server = Server::factory()->create(['status' => 'pending']);

    expect($server->isReadyForProvisioning())->toBeTrue();

    Process::assertRan(function ($process, $result) {
        Log::info("Process command: {$process->command}");

        return true;
    });

    // Assert both tasks (pwd and apt lock) exist
    $tasks = $server->tasks()->orderBy('id')->get();
    expect($tasks->count())->toBeGreaterThanOrEqual(2);
    $pwdTask = $tasks->first(fn ($task) => str_contains($task->script, 'pwd'));
    $aptLockTask = $tasks->first(fn ($task) => str_contains($task->script, 'lsof | grep /var/lib/dpkg/lock'));
    expect($pwdTask)->not->toBeNull();
    expect($aptLockTask)->not->toBeNull();
});

it('considers the server not ready if the working directory is not /root', function () {
    Process::fake([
        '*' => Process::sequence()
            // pwd task
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: '/not-root')), // Execute script
    ]);

    $server = Server::factory()->create(['status' => 'pending']);

    expect($server->isReadyForProvisioning())->toBeFalse();
});

it('considers the server not ready if the apt lock check fails', function () {
    Process::fake([
        '*' => Process::sequence()
            // pwd task
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: '/root')) // Execute script

            // apt lock status task
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: '', exitCode: 0)), // Execute script (empty output, fail)
    ]);

    $server = Server::factory()->create(['status' => 'pending']);

    expect($server->isReadyForProvisioning())->toBeFalse();
});
