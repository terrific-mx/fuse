<?php

use App\Jobs\FinishTaskJob;
use App\Models\Task;
use Illuminate\Support\Facades\Process;

it('updates the task status to finished, sets the exit code, and stores output', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Test output from remote log',
            exitCode: 0,
        ),
    ]);

    $task = Task::factory()->running()->create(['exit_code' => null]);
    $job = new FinishTaskJob($task, 42);

    $job->handle();

    $task->refresh();
    expect($task->status)->toBe('finished');
    expect($task->exit_code)->toBe(42);
    expect($task->output)->toBe('Test output from remote log');

    Process::assertRan(function ($process, $result) use ($task) {
        return str_contains($process->command, 'tail --bytes=2000000') &&
            $process->timeout === 10 &&
            str_contains($process->command, $task->fuseDirectory()."/task-{$task->id}.log");
    });
});

it('executes all after actions', function () {
    $mockCallback = new class
    {
        public function __invoke($task)
        {
            $GLOBALS['__after_action_called'] = true;
        }
    };
    app()->instance('mock.callback', $mockCallback);
    $task = Task::factory()->running()->create([
        'after_actions' => [
            ['class' => 'mock.callback'],
        ],
    ]);
    $job = new FinishTaskJob($task, 0);

    $job->handle();

    expect($task->fresh()->status)->toBe('finished');
    expect($GLOBALS['__after_action_called'] ?? false)->toBeTrue();
    unset($GLOBALS['__after_action_called']);
});

it('handles no after actions gracefully', function () {
    $task = Task::factory()->running()->create(['after_actions' => null]);
    $job = new FinishTaskJob($task, 0);

    $job->handle();

    expect($task->fresh()->status)->toBe('finished');
});
