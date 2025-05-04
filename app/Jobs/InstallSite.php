<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\Site;
use App\Scripts\InstallCaddyfile;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallSite implements ShouldQueue
{
    use Queueable;

    public Server $server;

    /**
     * Create a new job instance.
     */
    public function __construct(public Site $site)
    {
        $this->server = $site->server;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->run(new InstallCaddyfile($this->site));

        $this->site->update(['status' => 'installed']);

        $this->server->run(new UpdateCaddyImports($this->server));
    }
}
