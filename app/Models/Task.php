<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
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
            'payload' => 'array',
            'callback' => 'string',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Run the provisioning steps for this task on its server.
     */
    public function provision(): void
    {
        $this->markRunning();

        $this->prepareRemoteDirectory();

        $this->uploadScript();

        $this->runScript();
    }

    protected function prepareRemoteDirectory(): void
    {
        \Illuminate\Support\Facades\Process::run(
            "ssh {$this->user}@{$this->server->ip_address} 'bash -s' <<TOKEN mkdir -p /var/www TOKEN"
        );
    }

    protected function uploadScript(): void
    {
        \Illuminate\Support\Facades\Process::run(
            "scp {$this->script} {$this->user}@{$this->server->ip_address}:/var/www/{$this->script}"
        );
    }

    protected function runScript(): void
    {
        \Illuminate\Support\Facades\Process::run(
            "ssh {$this->user}@{$this->server->ip_address} 'bash /var/www/{$this->script}'"
        );
    }

    protected function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }

}
