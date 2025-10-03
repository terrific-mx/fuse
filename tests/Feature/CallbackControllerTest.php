<?php

use App\Callbacks\MarkServerProvisioned;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

it('returns a task via callback route', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $task = Task::factory()->create([
        'server_id' => $server->id,
        'status' => 'pending',
        'after_actions' => [
            (new MarkServerProvisioned)->toCallbackArray(),
        ],
    ]);

    $response = get(URL::signedRoute('task.callback', ['task' => $task]) . '&exit_code=0');

    $response->assertStatus(200);

    expect($task->fresh()->status)->toBe('finished');
    expect($server->fresh()->status)->toBe('provisioned');
});
