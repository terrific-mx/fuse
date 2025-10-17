<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\SshKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AuthorizeSshKeyOnServerJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SshKey $sshKey,
        public Server $server
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->sshKey->authorize($this->server);
    }
}
