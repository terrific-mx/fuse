<?php

namespace App\Jobs;

use App\Models\Cronjob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallCronjobJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Cronjob $cronjob
    ) {}

    public function handle(): void
    {
        $task = $this->cronjob->server->createInstallCronjobTask($this->cronjob)->run();
        $this->cronjob->update(['status' => 'installed']);
    }

    public function failed(\Throwable $exception): void
    {
        $this->cronjob->update(['status' => 'failed']);
    }
}
