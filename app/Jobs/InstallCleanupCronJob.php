<?php

namespace App\Jobs;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallCleanupCronJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Server $server,
    ) {}

    public function handle(): void
    {
        $this->server->createInstallCleanupCronTask()->run();
    }
}
