<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\Server;
use App\Scripts\UpdateCaddyfile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateApplicationCaddyFile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public Application $application)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->run(new UpdateCaddyfile($this->application));
    }
}
