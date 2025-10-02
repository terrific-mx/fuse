<?php

namespace App\Callbacks;

use App\Models\Deployment;
use App\Models\Task;

class UpdateDeploymentStatus
{
    public function __construct(public int $deployment_id) {}

    /**
     * Update the deployment status.
     */
    public function __invoke(Task $task)
    {
        $deployment = Deployment::findOrFail($this->deployment_id);

        $deployment->update(['status' => 'deployed']);
    }

    /**
     * Get the array representation of this callback for storing in the task.
     *
     * @return array{class: string, args: array<string, mixed>}
     */
    public function toCallbackArray(): array
    {
        return [
            'class' => self::class,
            'args' => [
                'deployment_id' => $this->deployment_id,
            ],
        ];
    }
}
