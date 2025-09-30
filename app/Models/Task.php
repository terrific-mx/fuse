<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Process;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The server that this task belongs to.
     */
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

    /**
     * Prepare the remote directory on the server.
     */
    protected function prepareRemoteDirectory(): void
    {
        Process::run(
            "ssh {$this->user}@{$this->server->ip_address} 'bash -s' <<TOKEN mkdir -p /var/www TOKEN"
        );
    }

    /**
     * Upload the provisioning script to the server.
     */
    protected function uploadScript(): void
    {
        Process::run(
            "scp {$this->script} {$this->user}@{$this->server->ip_address}:/var/www/{$this->script}"
        );
    }

    /**
     * Run the provisioning script on the server.
     */
    protected function runScript(): void
    {
        Process::run(
            "ssh {$this->user}@{$this->server->ip_address} 'bash /var/www/{$this->script}'"
        );
    }

    /**
     * Mark this task as running.
     */
    protected function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }
}
