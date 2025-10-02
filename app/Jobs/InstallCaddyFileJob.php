<?php

namespace App\Jobs;

use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        //
    }
}
