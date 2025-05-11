<?php

namespace App\Callbacks;

use App\Models\Task;

class CheckProvisioning
{
    public function handle(Task $task)
    {
        $task->successful()
            ? $task->server?->update(['status' => 'provisioned'])
            : $task->server?->update(['status' => 'failed']);
    }
}
