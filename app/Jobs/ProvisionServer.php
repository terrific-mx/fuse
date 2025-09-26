<?php

namespace App\Jobs;

use App\Models\Server;
use Exception;
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
            throw new Exception('Server too old for provisioning');
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
            $this->afterProvisioned();
            $this->delete();
            return;
        }

        $this->server->runProvisioningScript();
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        $this->server->delete();
    }

    /**
     * Perform any tasks after the server is provisioned.
     *
     * @return void
     */
    protected function afterProvisioned(): void
    {
        // Stub: Add post-provisioning logic here
    }
}
