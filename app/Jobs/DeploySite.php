<?php

namespace App\Jobs;

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

        $server->tasks()->create([
            'name' => 'deploy',
            'status' => 'pending',
            // Add other required fields if needed (user, script, etc.)
        ]);
    }
}
