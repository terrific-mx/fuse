<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

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
    /**
     * Get the remote home directory for the user.
     */
    public function remoteHomeDirectory(): string
    {
        return $this->user === 'root'
            ? '/root'
            : "/home/{$this->user}";
    }

    /**
     * The .fuse directory path for the user.
     */
    public function fuseDirectory(): string
    {
        return $this->remoteHomeDirectory() . '/.fuse';
    }

    /**
     * The remote script path for the user.
     */
    public function remoteScriptPath(): string
    {
        return $this->fuseDirectory() . '/' . basename($this->script);
    }

    /**
     * Prepare the remote directory on the server.
     */
    protected function prepareRemoteDirectory(): void
    {
        $remotePath = $this->fuseDirectory();

        $token = Str::random(20);

        $command = <<<SSH
            ssh {$this->user}@{$this->server->ip_address} 'bash -s' <<{$token}
            mkdir -p {$remotePath}
            {$token}
            SSH;

        Process::timeout(10)->run($command);
    }

    /**
     * Upload the provisioning script to the server.
     */
    protected function uploadScript(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();
        Process::run(
            "scp {$this->script} {$this->user}@{$this->server->ip_address}:{$remoteScriptPath}"
        );
    }

    /**
     * Run the provisioning script on the server.
     */
    protected function runScript(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();
        Process::run(
            "ssh {$this->user}@{$this->server->ip_address} 'bash {$remoteScriptPath}'"
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
