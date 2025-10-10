<?php

use App\Callbacks\MarkServerProvisioned;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

it('returns a task via callback route', function () {
    $task = Task::factory()->running()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]) . '&exit_code=0');

    $response->assertStatus(200);

    expect($task->fresh()->status)->toBe('finished');
});

it('returns 404 unless task status is running', function () {
    $task = Task::factory()->finished()->create();

    $response = get(URL::signedRoute('task.callback', ['task' => $task]) . '&exit_code=0');

    $response->assertStatus(404);
});
