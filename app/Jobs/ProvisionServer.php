<?php

namespace App\Jobs;

use App\Exceptions\ProvisioningTimeout;
use App\Models\Server;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProvisionServer implements ShouldQueue
{
    use Queueable;

    public $tries = 40; // 20 Total Minutes...

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->isProvisioned()) {
            try {
                $this->provisioned();
            } catch (Exception $e) {
                report($e);
            }

            $this->delete();

            return;
        }

        if ($this->server->olderThan(15)) {
            $this->fail(ProvisioningTimeout::for($this->server));

            return;
        }

        if ($this->server->isProvisioning()) {
            $this->release(30);

            return;
        }

        if ($this->server->isReadyForProvisioning()) {
            $this->server->runProvisioningScript();
        }

        $this->release(30);
    }

    /**
     * Perform any tasks after the server has been provisioned.
     */
    protected function provisioned()
    {
        //
    }
}
