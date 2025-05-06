<?php

namespace App\Jobs;

use App\Models\Server;
use App\Scripts\DeleteFolder;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/** @package App\Jobs */
class DeleteSite implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public string $path)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->run(new UpdateCaddyImports($this->server));
        $this->server->run(new DeleteFolder($this->path));
    }
}
