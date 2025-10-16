<?php

use App\Jobs\InstallDaemonJob;
use App\Models\Daemon;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an install_daemon task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'daemon installed')), // Execute script
    ]);

    $server = Server::factory()->create();
    $daemon = Daemon::factory()->for($server)->create([
        'command' => 'php artisan queue:work',
        'directory' => '/var/www/app',
        'user' => 'forge',
        'processes' => 2,
        'stop_wait_seconds' => 10,
        'stop_signal' => 'TERM',
    ]);

    $job = new InstallDaemonJob($daemon);
    $job->handle();

    expect($daemon->fresh()->installed_at)->not()->toBeNull();

    $task = $server->tasks()
        ->where('name', 'install_daemon')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain($daemon->command);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});
