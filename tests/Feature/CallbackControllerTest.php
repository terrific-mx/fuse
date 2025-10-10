<?php

use App\Models\Task;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

use Illuminate\Support\Facades\Queue;
use App\Jobs\UpdateTaskStatusJob;

beforeEach(function () {
    Queue::fake();
});

it('dispatches a job to update the task status via callback route', function () {
    $task = Task::factory()->running()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]) . '&exit_code=0');

    $response->assertStatus(200);

    Queue::assertPushed(UpdateTaskStatusJob::class, function ($job) use ($task) {
        return $job->task->is($task) && $job->exitCode === 0;
    });
});

it('returns 404 unless task status is running', function () {
    $task = Task::factory()->finished()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]) . '&exit_code=0');

    $response->assertStatus(404);
});
