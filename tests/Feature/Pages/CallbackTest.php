<?php

use App\Jobs\FinishTask;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('distpaches a job to finish the running task', function () {
    Queue::fake();
    $task = Task::factory()->running()->create();

    get(URL::signedRoute('callback', ['task' => $task->id], absolute: false));

    Queue::assertPushed(FinishTask::class);
});

it('retruns 404 when task is not running anymor', function () {
    $task = Task::factory()->finished()->create();

    get(URL::signedRoute('callback', ['task' => $task->id], absolute: false))
        ->assertNotFound();
});

it('allows appending status code in query string while signing the rououte', function () {
    Queue::fake();
    $task = Task::factory()->running()->create();

    get(URL::signedRoute('callback', ['task' => $task->id], absolute: false).'&exit_code=0');

    Queue::assertPushed(FinishTask::class);
});

it('validates signature', function () {
    $task = Task::factory()->running()->create();

    get(route('callback', ['task' => $task->id], absolute: false))
        ->assertStatus(401);
});
