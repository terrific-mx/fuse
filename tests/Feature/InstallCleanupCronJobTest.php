<?php

use App\Jobs\InstallCleanupCronJob;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an install_cleanup_cron task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result()), // Execute script
    ]);

    $server = Server::factory()->create();
    $job = new InstallCleanupCronJob($server);

    $job->handle();

    $task = $server->tasks()
        ->where('name', 'install_cleanup_cron')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain('find /root/.fuse -name "task-*" -type f -mtime +7 -exec rm {}');
    expect($task->script)->toContain('find /home/eddy/.fuse -name "task-*" -type f -mtime +7 -exec rm {}');
    expect($task->script)->toContain('/etc/cron.d/fuse-task-cleanup');

    Process::assertRan(function ($process, $result) {
        return true;
    });
});
