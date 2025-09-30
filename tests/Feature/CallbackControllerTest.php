<?php

use App\Models\Server;
use App\Models\Task;

use function Pest\Laravel\get;

it('returns a task via callback route', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $task = Task::factory()->create([
        'server_id' => $server->id,
        'status' => 'pending',
        'callback' => \App\Callbacks\MarkServerProvisioned::class,
    ]);

    $response = get("/callback/task/{$task->id}");

    $response->assertStatus(200);

    $response->assertJson([
        'id' => $task->id,
        'status' => 'finished',
    ]);

    expect($task->fresh()->status)->toBe('finished');
    expect($server->fresh()->status)->toBe('provisioned');
});
