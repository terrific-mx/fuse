<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Task;

class UpdateTaskStatusJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public int $exitCode = 0
    ) {}

    public function handle(): void
    {
        $this->task->update(['status' => 'finished', 'exit_code' => $this->exitCode]);

        foreach ($this->task->after_actions ?? [] as $callback) {
            if (!isset($callback['class'])) {
                continue;
            }

            $instance = app()->makeWith($callback['class'], $callback['args'] ?? []);
            $instance($this->task);
        }
    }
}

