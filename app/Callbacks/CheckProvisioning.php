<?php

namespace App\Callbacks;

use App\Jobs\UpdateServerPublicKey;
use App\Models\Task;

class CheckProvisioning
{
    public function handle(Task $task)
    {
        optional($task->server, function ($server) use ($task) {
            $server->update([
                'status' => $task->successful() ? 'provisioned' : 'failed',
            ]);

            if ($task->successful()) {
                UpdateServerPublicKey::dispatch($server);
            }
        });
    }
}
