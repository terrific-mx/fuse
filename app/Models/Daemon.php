<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daemon extends Model
{
    /** @use HasFactory<\Database\Factories\DaemonFactory> */
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
            'processes' => 'integer',
            'stop_wait_seconds' => 'integer',
            'installed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    protected function outputPath(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user === 'root'
                ? "/root/.fuse/daemon-{$this->id}.log"
                : "/home/{$this->user}/.fuse/daemon-{$this->id}.log",
        );
    }

    protected function errorPath(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user === 'root'
                ? "/root/.fuse/daemon-{$this->id}.err"
                : "/home/{$this->user}/.fuse/daemon-{$this->id}.err",
        );
    }

    protected function configPath(): Attribute
    {
        return Attribute::make(
            get: fn () => "/etc/supervisor/conf.d/daemon-{$this->id}.conf",
        );
    }

    public function install(): void
    {
        $this->server->createInstallDaemonTask($this)->run();

        $this->markAsInstalled();
    }

    public function markAsInstalled(): void
    {
        $this->update(['installed_at' => now()]);
    }

    public function markAsFailed(): void
    {
        $this->update(['failed_at' => now()]);
    }
}
