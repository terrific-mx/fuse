<?php

use App\Jobs\FinishTaskJob;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

beforeEach(function () {
    Queue::fake();
});

it('dispatches a job to update the task status via callback route', function () {
    $task = Task::factory()->running()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]).'&exit_code=0');

    $response->assertStatus(200);

    Queue::assertPushed(FinishTaskJob::class, function ($job) use ($task) {
        return $job->task->is($task) && $job->exitCode === 0;
    });
});

it('returns 404 unless task status is running', function () {
    $task = Task::factory()->finished()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]).'&exit_code=0');

    $response->assertStatus(404);
});
