<?php

namespace App\Callbacks;

use App\Jobs\UpdateServerPublicKey;
use App\Models\Task;

class CheckProvisioning
{
    public function handle(Task $task)
    {
        if (! $task->server) {
            return;
        }

        $task->successful()
            ? $task->server->update(['status' => 'provisioned'])
            : $task->server->update(['status' => 'failed']);

        if ($task->successful()) {
            UpdateServerPublicKey::dispatch($task->server);
        }
    }
}
