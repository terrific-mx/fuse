<?php

namespace App\Jobs;

use App\Models\Database;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallDatabaseJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Database $database
    ) {}

    public function handle(): void
    {
        $this->database->install();
    }

    public function failed(\Throwable $exception): void
    {
        $this->database->markAsFailed();
    }
}
