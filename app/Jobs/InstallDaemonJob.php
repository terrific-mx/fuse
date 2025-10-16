<?php

namespace App\Jobs;

use App\Models\Daemon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallDaemonJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Daemon $daemon,
    ) {}

    public function handle(): void
    {
        $this->daemon->install();
    }
}
