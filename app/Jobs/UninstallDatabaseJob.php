<?php

namespace App\Jobs;

use App\Models\Database;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class UninstallDatabaseJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Database $database
    ) {}

    public function handle(): void
    {
        $task = $this->database->server->createUninstallDatabaseTask($this->database)->run();

        if (! $task->isSuccessful()) {
            $this->fail(new \Exception('Uninstall database task failed'));

            return;
        }

        $this->database->delete();
    }

    public function failed(\Throwable $exception): void
    {
        $this->database->markAsInstalled();
    }
}
