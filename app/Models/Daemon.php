<?php

namespace App\Models;

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
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function outputLogPath(): string
    {
        return "/var/log/daemon-{$this->id}.out.log";
    }

    public function errorLogPath(): string
    {
        return "/var/log/daemon-{$this->id}.err.log";
    }
}
