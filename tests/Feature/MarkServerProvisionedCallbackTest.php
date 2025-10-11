<?php

use App\Callbacks\MarkServerProvisioned;
use App\Jobs\RetrieveRemoteSshKey;
use App\Models\Server;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;

it('marks a server as provisioned and dispatches a job to retrieve the remote public ssh key', function () {
    Queue::fake();
    $server = Server::factory()->pending()->create();
    $task = Task::factory()->pending()->for($server)->create();
    $callback = new MarkServerProvisioned;

    $callback($task);

    expect($server->fresh()->status)->toBe('provisioned');

    Queue::assertPushed(RetrieveRemoteSshKey::class, function (RetrieveRemoteSshKey $job) use ($server) {
        return $job->server->is($server);
    });
});
