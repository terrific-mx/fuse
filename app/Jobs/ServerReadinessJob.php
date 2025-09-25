<?php

namespace App\Jobs;

use App\Models\Server;
use App\Notifications\ServerFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

class ServerReadinessJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ip = $this->server->ip_address;
        $user = 'root';
        $process = Process::run("ssh -o BatchMode=yes -o ConnectTimeout=5 $user@$ip exit");

        if ($process->exitCode() === 0) {
            $this->server->update(['status' => 'ready']);

            return;
        }

        $this->release();
    }

    /**
     * Handle a job failure.
     */
    public function failed(): void
    {
        $this->server->update(['status' => 'failed']);

        $this->server->organization->user->notify(new ServerFailedNotification());
    }
}
