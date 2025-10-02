<?php

namespace App\Jobs;

use App\Callbacks\UpdateDeploymentStatus;
use App\Models\Deployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeploySite implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Deployment $deployment) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $server = $this->deployment->site->server;

        $task = $server->tasks()->create([
            'name' => 'deploy',
            'status' => 'pending',
            'user' => 'fuse',
            'script' => view('scripts.site.deploy', [
                'server' => $server,
                'site' => $this->deployment->site,
                'deployment' => $this->deployment,
            ])->render(),
            'after_actions' => [
                (new UpdateDeploymentStatus($this->deployment->id))->toCallbackArray(),
            ],
        ]);

        $task->provision();
    }
}
