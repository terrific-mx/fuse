<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeauthorizeSshKeyOnServerJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public \App\Models\SshKey $sshKey,
        public \App\Models\Server $server
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        tap($this->sshKey->deauthorize($this->server), function ($task) {
            throw_if($task->isFailed(), \Exception::class, 'SSH key deauthorization failed on server');
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
