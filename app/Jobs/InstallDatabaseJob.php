<?php

namespace App\Jobs;

use App\Models\Database;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallDatabaseJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Database $database
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
