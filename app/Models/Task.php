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

        $this->wrapScriptWithCallback();

        $this->prepareRemoteDirectory();

        $this->uploadScript();

        $this->runScriptInBackground();
    }

    /**
     * Wrap the raw script with callback logic and save it to the model.
     */
    protected function wrapScriptWithCallback(): void
    {
        $path = $this->fuseDirectory().'/task-'.$this->id.'-'.Str::random(8).'.sh';

        $this->update([
            'script' => view('scripts.task-callback', [
                'task' => $this,
                'path' => $path,
                'token' => Str::random(20),
            ])->render(),
        ]);
    }

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
    private function runProcess(string $command, int $timeout = 60): void
    {
        Process::timeout($timeout)->run($command);
    }

    /**
     * Prepare the remote directory on the server.
     */
    protected function prepareRemoteDirectory(): void
    {
        $remotePath = $this->fuseDirectory();

        $this->executeScriptOnRemoteServer("mkdir -p {$remotePath}", 10);
    }

    /**
     * Execute a shell script on the remote server via SSH using heredoc for multi-line scripts.
     */
    protected function executeScriptOnRemoteServer(string $script, int $timeout = 60): void
    {
        $heredocToken = Str::random(20);

        $sshKeyService = app(OrganizationSshKeyService::class);

        $privateKeyPath = $sshKeyService->writePrivateKeyToStorage($this->server->organization);

        $sshOptions = $this->buildSshOptions($privateKeyPath);

        $fullCommand = <<<SSH
            ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'bash -s' <<{$heredocToken}
            {$script}
            {$heredocToken}
        SSH;

        $this->runProcess($fullCommand, $timeout);

        $sshKeyService->deletePrivateKeyFromStorage($this->server->organization);
    }

    /**
     * Write the raw script content to a file in storage/app/scripts and return its path.
     */
    private function writeScriptToStorageDir(): string
    {
        $dir = storage_path('app/scripts');
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $filename = 'task-script-'.Str::random(20).'.sh';
        $path = $dir.DIRECTORY_SEPARATOR.$filename;
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

        $organization = $this->server->organization;
        $sshKeyService = app(OrganizationSshKeyService::class);
        $privateKeyPath = $sshKeyService->writePrivateKeyToStorage($organization);

        try {
            $scpOptions = $this->buildScpOptions($privateKeyPath);
            $command = "scp {$scpOptions} {$storageScriptFile} {$this->user}@{$this->server->ip_address}:{$remoteScriptPath}";
            $this->runProcess($command);
        } finally {
            $sshKeyService->deletePrivateKeyFromStorage($organization);
            if (file_exists($storageScriptFile)) {
                unlink($storageScriptFile);
            }
        }
    }

    /**
     * Run the script on the server in the background.
     */
    protected function runScriptInBackground(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();

        $organization = $this->server->organization;
        $sshKeyService = app(OrganizationSshKeyService::class);
        $privateKeyPath = $sshKeyService->writePrivateKeyToStorage($organization);

        try {
            $sshOptions = $this->buildSshOptions($privateKeyPath);
            $outputLogPath = $this->fuseDirectory().'/task-'.$this->id.'.log';
            $command = "ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'nohup bash {$remoteScriptPath} >> {$outputLogPath} 2>&1 &'";

            $this->runProcess($command);
        } finally {
            $sshKeyService->deletePrivateKeyFromStorage($organization);
        }
    }

    /**
     * Mark this task as running.
     */
    protected function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }

    /**
     * Get the timeout duration for the task in seconds.
     */
    public function timeout(): int
    {
        return 3600;
    }
}
