<?php

namespace App\Models;

use App\Services\OrganizationSshKeyService;
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
        return $this->remoteHomeDirectory().'/.fuse';
    }

    /**
     * The remote script path for the user.
     */
    public function remoteScriptPath(): string
    {
        return $this->fuseDirectory().'/'.basename($this->script);
    }

    /**
     * Run a callback with the organization's SSH private key, ensuring cleanup.
     */
    private function withOrganizationSshKey(callable $callback): void
    {
        $organization = $this->server->organization;
        $sshKeyService = app(OrganizationSshKeyService::class);
        $privateKeyPath = $sshKeyService->writePrivateKeyToStorage($organization);

        try {
            $callback($privateKeyPath);
        } finally {
            $sshKeyService->deletePrivateKeyFromStorage($organization);
        }
    }

    /**
     * Build SSH options string for commands.
     */
    private function buildSshOptions(string $privateKeyPath): string
    {
        return "-o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i {$privateKeyPath}";
    }

    /**
     * Build SCP options string for commands.
     */
    private function buildScpOptions(string $privateKeyPath): string
    {
        return $this->buildSshOptions($privateKeyPath);
    }

    /**
     * Run a process with optional timeout.
     */
    private function runProcess(string $command, int $timeout = 10): void
    {
        Process::timeout($timeout)->run($command);
    }

    /**
     * Prepare the remote directory on the server.
     */
    protected function prepareRemoteDirectory(): void
    {
        $remotePath = $this->fuseDirectory();
        $token = Str::random(20);

        $this->withOrganizationSshKey(function ($privateKeyPath) use ($remotePath, $token) {
            $sshOptions = $this->buildSshOptions($privateKeyPath);
            $command = <<<SSH
                ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'bash -s' <<{$token}
                mkdir -p {$remotePath}
                {$token}
                SSH;

            $this->runProcess($command);
        });
    }

    /**
     * Write the raw script content to a file in storage/app/scripts and return its path.
     */
    private function writeScriptToStorageDir(): string
    {
        $dir = storage_path('app/scripts');
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $filename = 'task-script-'.Str::random(20).'.sh';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, $this->script);
        return $path;
    }

    /**
     * Upload the provisioning script to the server.
     */
    protected function uploadScript(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();
        $storageScriptFile = $this->writeScriptToStorageDir();

        try {
            $this->withOrganizationSshKey(function ($privateKeyPath) use ($remoteScriptPath, $storageScriptFile) {
                $scpOptions = $this->buildScpOptions($privateKeyPath);
                $command = "scp {$scpOptions} {$storageScriptFile} {$this->user}@{$this->server->ip_address}:{$remoteScriptPath}";
                $this->runProcess($command);
            });
        } finally {
            if (file_exists($storageScriptFile)) {
                unlink($storageScriptFile);
            }
        }
    }

    /**
     * Run the provisioning script on the server.
     */
    protected function runScript(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();

        $this->withOrganizationSshKey(function ($privateKeyPath) use ($remoteScriptPath) {
            $sshOptions = $this->buildSshOptions($privateKeyPath);
            $command = "ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'bash {$remoteScriptPath}'";

            $this->runProcess($command);
        });
    }

    /**
     * Mark this task as running.
     */
    protected function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }
}
