<?php

namespace App\Callbacks;

use App\Models\Deployment;
use App\Models\Task;

class CheckDeployment
{
    public function __construct(public int $id) {}

    public function handle(Task $task)
    {
        if ($deployment = Deployment::find($this->id)) {
            $task->successful()
                ? $deployment->update(['status' => 'finished'])
                : $deployment->update(['status' => 'failed']);
        }
    }
}
