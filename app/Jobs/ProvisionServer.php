<?php

namespace App\Jobs;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 40;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->is_provisioned) {
            $this->delete();

            return;
        }

        if ($this->server->is_provisioning) {
            $this->release(30);

            return;
        }

        if ($this->server->isOlderThanMinutes(15)) {
            $this->fail();

            return;
        }

        if ($this->server->isReadyForProvisioning()) {
            $this->server->provision();
        }

        $this->release(30);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->server->delete();
    }
}
