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
        if ($this->server->isOlderThanMinutes(15)) {
            $this->fail();
            return;
        }

        if (! $this->server->isReadyForProvisioning()) {
            $this->release(30);
            return;
        }

        if ($this->server->isProvisioning()) {
            $this->release(30);
            return;
        }

        if ($this->server->isProvisioned()) {
            $this->delete();
            return;
        }

        $this->server->runProvisioningScript();
    }
}
