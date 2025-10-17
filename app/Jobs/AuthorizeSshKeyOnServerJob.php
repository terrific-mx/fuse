<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AuthorizeSshKeyOnServerJob implements ShouldQueue
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
        $script = <<<BASH
mkdir -p ~/.ssh
chmod 700 ~/.ssh

echo '{$this->sshKey->public_key}' >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
BASH;

        $task = $this->server->tasks()->create([
            'name' => 'authorize_ssh_key',
            'user' => 'fuse',
            'script' => $script,
            'payload' => [],
            'after_actions' => [],
        ]);

        $task->run();
    }
}
