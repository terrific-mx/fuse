<?php

namespace App\Callbacks;

use App\Models\Task;

class MarkServerProvisioned
{
    /**
     * Mark the given server as provisioned.
     */
    public function __invoke(Task $task)
    {
        $task->server->afterProvisioned();
    }

    /**
     * Get the array representation of this callback for storing in the task.
     *
     * @return array{class: string, args: array<int, mixed>}
     */
    public function toCallbackArray(): array
    {
        return [
            'class' => self::class,
        ];
    }
}
