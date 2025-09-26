<?php

namespace App\Jobs;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->server->isReadyForProvisioning()) {
            // Release the job for 30 seconds if not ready
            $this->release(30);
            return;
        }

        $this->server->runProvisioningScript();
    }
}
