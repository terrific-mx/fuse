<?php

use App\Jobs\FinishTaskJob;
use App\Models\Task;

it('updates the task status to finished and sets the exit code', function () {
    $task = Task::factory()->running()->create(['exit_code' => null]);
    $job = new FinishTaskJob($task, 42);

    $job->handle();

    $task->refresh();
    expect($task->status)->toBe('finished');
    expect($task->exit_code)->toBe(42);
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
