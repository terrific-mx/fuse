<?php

namespace App\Jobs;

use App\Models\Server;
use App\Scripts\GetPublicSshKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateServerPublicKey implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = $this->server->run(new GetPublicSshKey($this->server));

        $this->server->update([
            'public_key' => trim($task->output),
        ]);
    }
}
