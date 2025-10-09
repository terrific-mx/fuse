<?php

namespace App\Callbacks;

use App\Models\Deployment;
use App\Models\Task;
use App\Jobs\InstallCaddyFileJob;

class UpdateDeploymentStatus
{
    public function __construct(public int $deployment_id) {}

    /**
     * Update the deployment status.
     */
    public function __invoke(Task $task)
    {
        $deployment = Deployment::findOrFail($this->deployment_id);

        $deployment->markDeployed();

        // Create a get_git_hash task for this deployment's site/server
        $task = $deployment->site->server->tasks()->create([
            'name' => 'get_git_hash',
            'user' => 'fuse',
            'script' => "git rev-list {$deployment->site->repository_branch} -1",
            'payload' => [],
            'after_actions' => [],
        ]);
        $task->run();
        $deployment->update(['commit' => $task->output]);
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
