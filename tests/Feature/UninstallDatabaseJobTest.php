<?php

use App\Jobs\UninstallDatabaseJob;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;

it('creates and runs an uninstall_database task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'database uninstalled')), // Execute script
    ]);

    $server = Server::factory()->create();
    $database = Database::factory()
        ->for($server)
        ->installed()
        ->create(['name' => 'my_database']);

    $job = new UninstallDatabaseJob($database);
    $job->handle();

    expect(Database::find($database->id))->toBeNull();

    $task = $server->tasks()
        ->where('name', 'uninstall_database')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain($database->name);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('stores the failed date when the job fails', function () {
    $database = Database::factory()->installed()->create();

    $job = new UninstallDatabaseJob($database);
    $job->failed(new \Exception('Simulated failure'));

    expect($database->fresh()->status)->toBe('installed');
});
