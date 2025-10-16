<?php

use App\Jobs\InstallDatabaseJob;
use App\Models\Database;
use App\Models\Server;
use Exception;
use Illuminate\Support\Facades\Process;

it('creates and runs an install_database task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'database installed')), // Execute script
    ]);

    $server = Server::factory()->create();
    $database = Database::factory()->for($server)->create([
        'name' => 'my_database',
    ]);

    $job = new InstallDatabaseJob($database);
    $job->handle();

    expect($database->fresh()->status)->toBe('installed');

    $task = $server->tasks()
        ->where('name', 'install_database')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain($database->name);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('stores the failed date when the job fails', function () {
    $database = Database::factory()->create();

    $job = new InstallDatabaseJob($database);
    $job->failed(new Exception('Simulated failure'));

    expect($database->fresh()->status)->toBe('failed');
});
