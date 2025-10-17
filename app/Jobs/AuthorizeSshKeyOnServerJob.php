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
        tap($this->sshKey->authorize($this->server), function ($task) {
            throw_if($task->isFailed(), \Exception::class, 'SSH key authorization failed on server');
        });
    }

    /**
     * Remove the association if the job fails.
     */
    public function failed(\Throwable $exception): void
    {
        $this->sshKey->servers()->detach($this->server->id);
    }
}
