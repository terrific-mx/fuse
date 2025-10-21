<?php

namespace App\Jobs;

use App\Models\Database;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UninstallDatabaseJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Database $database
    ) {}

    public function handle(): void
    {
        $this->database->purge();
    }

    public function failed(\Throwable $exception): void
    {
        $this->database->markAsInstalled();
    }
}
