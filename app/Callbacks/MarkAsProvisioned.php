<?php

namespace App\Callbacks;

use App\Models\Task;

class MarkAsProvisioned
{
    public function handle(Task $task)
    {
        $task->server?->markAsProvisioned();
    }
}
