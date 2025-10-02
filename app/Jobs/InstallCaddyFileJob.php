<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class InstallCaddyFileJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Site $site)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $server = $this->site->server;

        $server->tasks()->create([
            'name' => 'install_caddy_file',
            'user' => 'fuse',
            'script' => view('scripts.site.install-caddy', [
                'site' => $this->site,
                'tempCaddyfilePath' => Str::of($this->site->caddyfile_path)->append('.' . Str::random(16)),
            ])->render(),
        ])->provision();
    }
}
