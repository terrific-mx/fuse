<?php

namespace App\Models;

use App\Jobs\InstallCronjobJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronjob extends Model
{
    /** @use HasFactory<\Database\Factories\CronjobFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'server_id' => 'integer',
            'status' => 'string',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Mark this cronjob as installing and dispatch the install job.
     */
    public function install(): void
    {
        if ($this->status === 'installing') {
            return;
        }
        $this->update(['status' => 'installing']);
        InstallCronjobJob::dispatch($this);
    }

    /**
     * Mark this cronjob as installed.
     */
    public function markAsInstalled(): void
    {
        $this->update(['status' => 'installed']);
    }

    /**
     * Mark this cronjob as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Get the cron expression for this cronjob.
     */
    public function expression(): string
    {
        if ($this->frequency === 'custom') {
            return $this->custom_expression;
        }

        return match ($this->frequency) {
            'every_minute' => '* * * * *',
            'every_5_minutes' => '*/5 * * * *',
            'hourly' => '0 * * * *',
            'daily' => '0 0 * * *',
            'weekly' => '0 0 * * 0',
            'monthly' => '0 0 1 * *',
            'on_reboot' => '@reboot',
            default => '* * * * *',
        };
    }

    /**
     * Get the log file path for this cronjob.
     */
    public function logPath(): string
    {
        if ($this->user === 'root') {
            return "/root/.fuse/cron-{$this->id}.log";
        }

        return "/home/{$this->user}/.fuse/cron-{$this->id}.log";
    }
}
