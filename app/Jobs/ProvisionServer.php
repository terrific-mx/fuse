<?php

namespace App\Jobs;

use App\Models\Server;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

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
        $this->server->update(['status' => 'provisioning']);

        // Create a task for provisioning scripts
        $task = $this->server->tasks()->create([
            'name' => 'provision',
            'user' => 'root',
            'script' => 'provision.sh',
            'payload' => [],
            'callback' => \App\Callbacks\MarkServerProvisioned::class,
        ]);

        // Ensure working directory exists on the remote server using the task user
        Process::run("ssh {$task->user}@{$this->server->ip_address} 'bash -s' <<TOKEN mkdir -p /var/www TOKEN");

        $task->update(['status' => 'running']);
    }
}
