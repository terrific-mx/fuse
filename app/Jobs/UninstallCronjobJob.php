<?php

namespace App\Jobs;

use App\Models\Cronjob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UninstallCronjobJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Cronjob $cronjob
    ) {}

    public function handle(): void
    {
        $task = $this->cronjob->server->createUninstallCronjobTask($this->cronjob)->run();

        if (! $task->isSuccessful()) {
            $this->fail(new \Exception('Uninstall cronjob task failed'));

            return;
        }

        $this->cronjob->delete();
    }

    public function failed(\Throwable $exception): void
    {
        $this->cronjob->markAsInstalled();
    }
}
