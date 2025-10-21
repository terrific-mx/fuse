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

        if (! $task->isSuccessful()) {
            $this->fail(new \Exception('Failed to install cronjob'));

            return;
        }

        $this->cronjob->markAsInstalled();
    }

    public function failed(\Throwable $exception): void
    {
        $this->cronjob->markAsFailed();
    }
}
