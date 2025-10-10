<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateTaskStatusJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public int $exitCode = 0
    ) {}

    public function handle(): void
    {
        $this->task->finish($this->exitCode);
    }
}
