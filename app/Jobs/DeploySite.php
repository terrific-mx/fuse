<?php

namespace App\Jobs;

use App\Models\Deployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeploySite implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 20;

    /**
     * Create a new job instance.
     */
    public function __construct(public Deployment $deployment) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->deployment->isFailed()) {
            $this->delete();

            return;
        }

        if ($this->deployment->isDeployed()) {
            $this->delete();

            return;
        }

        if ($this->deployment->isDeploying()) {
            $this->release(30);

            return;
        }

        if ($this->deployment->isStale()) {
            $this->fail();

            return;
        }

        $this->deployment->deploy();

        $this->release(30);
    }

    /**
     * Handle a job failure.
     */
    public function failed(): void
    {
        $this->deployment->markFailed();
    }
}
