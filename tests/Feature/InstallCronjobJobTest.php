<?php

use App\Jobs\InstallCronjobJob;
use App\Models\Cronjob;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an install_cronjob task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'cronjob installed')), // Execute script
    ]);

    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()
        ->for($server)
        ->pending()
        ->create([
            'command' => 'php artisan schedule:run',
            'user' => 'root',
            'frequency' => 'hourly',
        ]);

    $job = new InstallCronjobJob($cronjob);
    $job->handle();

    expect($cronjob->fresh()->status)->toBe('installed');

    $task = $server->tasks()
        ->where('name', 'install_cronjob')
        ->where('user', $cronjob->user)
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain($cronjob->command);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('sets status to failed when the job fails', function () {
    $cronjob = Cronjob::factory()->pending()->create();

    $job = new InstallCronjobJob($cronjob);
    $job->failed(new \Exception('Simulated failure'));

    expect($cronjob->fresh()->status)->toBe('failed');
});
