<?php

use App\Models\Task;
use App\ShellResult;
use Facades\App\ShellProcessRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;

uses(RefreshDatabase::class);

it('marks a task with finished status after successful execution', function () {
    Process::fake();
    $task = Task::factory()->create();

    $task->run();

    expect($task->fresh())
        ->status->toBe('finished');
});

it('ensures the working directory exists for the task execution', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->withArgs(function ($command) use ($task) {
        expect($command)
            ->toContain('mkdir -p')
            ->toContain('/root/.fuse')
            ->toContain($task->server->public_address)
            ->toContain($task->server->ownerKeyPath());

        return true;
    })->once();

    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Upload
        new ShellResult(0, ''), // Script Result
    ])->twice();

    $task->run();
});

it('uploads the script to the remote server using SCP with proper authentication', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Upload
    ])->once();

    ShellProcessRunner::shouldReceive('run')->withArgs(function ($command) use ($task) {
        expect($command)->toContain('scp -i')
            ->toContain('/root/.fuse')
            ->toContain($task->server->public_address)
            ->toContain($task->server->ownerKeyPath());

        return true;
    })->andReturn(new ShellResult(0, ''))->once();

    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Script Result
    ])->once();

    $task->run();
});

it('updates task status, exit code, and output with script execution results when script runs successfully', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Directory Exists
        new ShellResult(0, ''), // Upload
        new ShellResult(0, 'Script result'), // Script Result
    ])->times(3);

    $task->run();

    expect($task->fresh())
        ->status->toBe('finished')
        ->exit_code->toBe(0)
        ->output->toBe('Script result');
});

it('marks a task as timed out when the remote script execution exceeds the timeout limit', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Directory Exists
        new ShellResult(0, ''), // Upload
        new ShellResult(timedOut: true, exitCode: 0, output: ''), // Script Result
    ])->times(3);

    $task->run();

    expect($task->fresh())
        ->status->toBe('timeout');
});

it('marks the task as timedout when script upload timesout', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Directory Exists
        new ShellResult(timedOut: true, exitCode: 1, output: ''), // Upload
    ])->twice();

    $task->run();

    expect($task->fresh())
        ->status->toBe('timeout');
});

it('finish a task marks the task as finished', function () {
    Process::fake();
    $task = Task::factory()->create();
    $task->finish();

    expect($task->isFinished())->toBeTrue();
    expect($task->exit_code)->toBe(0);
});

it('finish a task marks the task as finished with exit code', function () {
    Process::fake();
    $task = Task::factory()->create();
    $task->finish(999);

    expect($task->isFinished())->toBeTrue();
    expect($task->exit_code)->toBe(999);
});

it('finish a task updates the output with the retrieved ooutput from the remote server', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, 'Output retrieved from remote server'),
    ])->once();

    $task->finish();

    expect($task->fresh()->output)->toBe('Output retrieved from remote server');
});

it('marks a task as running when executing a script in background mode', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Working Directory Exists
        new ShellResult(0, ''), // Upload
        new ShellResult(0, ''), // Run Script on Remote Server
    ])->times(3);

    $task->runInBackground();

    expect($task->fresh())->status->toBe('running');
});

it('executes callback when running script in background', function () {
    $task = Task::factory()->create();
    $mockResponse = [
        new ShellResult(0, ''), // Ensure working directory exists
        new ShellResult(0, ''), // Upload script
        new ShellResult(0, ''), // Execute script on remote server
    ];
    ShellProcessRunner::shouldReceive('run')
        ->andReturn(...$mockResponse)
        ->times(3);

    $task->runInBackground();

    expect($task->fresh())
        ->script
        ->toContain('callback');
});

it('first verifies the working directory exists on the remote server when running a script in background mode', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->withArgs(function ($command) use ($task) {
        expect($command)
            ->toContain('mkdir -p')
            ->toContain('/root/.fuse')
            ->toContain($task->server->public_address)
            ->toContain($task->server->ownerKeyPath());

        return true;
    })->once();

    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Upload
        new ShellResult(0, ''), // Run Script on Remote Server
    ])->twice();

    $task->runInBackground();
});

it('running a script in background uploads the script to the remote server', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn([
        new ShellResult(0, ''), // Ensure Working Directory Exists
    ])->once();

    ShellProcessRunner::shouldReceive('run')->withArgs(function ($command) use ($task) {
        expect($command)
            ->toContain('scp -i')
            ->toContain('/root/.fuse')
            ->toContain($task->server->public_address)
            ->toContain($task->server->ownerKeyPath());

        return true;
    })->andReturn(new ShellResult(0, ''))->once();

    ShellProcessRunner::shouldReceive('run')->andReturn([
        new ShellResult(0, ''), // Execute script on remote server
    ])->once();

    $task->runInBackground();
});

it('marks the task as timed out when script upload fails during background execution', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Directory Exists
        new ShellResult(timedOut: true, exitCode: 1, output: ''), // Upload
    ])->twice();

    $task->runInBackground();

    expect($task->fresh())
        ->status->toBe('timeout');
});

it('executes all required shell processes when running a script in background mode', function () {
    $task = Task::factory()->create();
    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, ''), // Ensure Directory Exists
        new ShellResult(0, ''), // Upload
        new ShellResult(0, ''), // Run Script on Remote Server
    ])->times(3);

    $task->runInBackground();
});

it('executes callback when task is completed successfully', function () {
    TestCallbackHandler::$called = false;

    $task = Task::factory()->running()->create([
        'options' => ['then' => [TestCallbackHandler::class]],
    ]);

    ShellProcessRunner::shouldReceive('run')->andReturn(...[
        new ShellResult(0, 'output'),
    ])->once();

    $task->finish();

    expect(TestCallbackHandler::$called)->toBeTrue();
});

class TestCallbackHandler
{
    public static $called = false;

    public function handle(Task $task)
    {
        static::$called = true;
    }
}
