<?php

use App\Jobs\UninstallCronjobJob;
use App\Models\Cronjob;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an uninstall_cronjob task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'cronjob uninstalled')), // Execute script
    ]);

    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()
        ->for($server)
        ->installed()
        ->create();

    $job = new UninstallCronjobJob($cronjob);
    $job->handle();

    expect(Cronjob::find($cronjob->id))->toBeNull();

    $task = $server->tasks()
        ->where('name', 'uninstall_cronjob')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain("/etc/cron.d/cron-{$cronjob->id}");

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('does not delete the cronjob if the uninstall task fails', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(exitCode: 1, output: 'failed to remove file')),
    ]);

    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()
        ->for($server)
        ->installed()
        ->create();

    $job = new UninstallCronjobJob($cronjob);
    $job->handle();

    expect(Cronjob::find($cronjob->id))->not()->toBeNull();
    expect($cronjob->fresh()->status)->toBe('installed');
});

it('marks the cronjob as installed when the job fails', function () {
    $cronjob = Cronjob::factory()->installed()->create();

    $job = new UninstallCronjobJob($cronjob);
    $job->failed(new \Exception('Simulated failure'));

    expect($cronjob->fresh()->status)->toBe('installed');
});
