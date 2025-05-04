<?php

use App\Jobs\FinishTask;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;

uses(RefreshDatabase::class);

it('finishes a task and updated the script exit code', function () {
    Process::fake();
    $task = Task::factory()->create();
    $job = new FinishTask($task, 999);

    $job->handle();

    expect($task->isFinished())->toBeTrue();
    expect($task->exit_code)->toBe(999);
});
