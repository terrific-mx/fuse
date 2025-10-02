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
    protected function casts()
    {
        return [
            'payload' => 'array',
            'after_actions' => 'array',
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
        $path = "{$this->fuseDirectory()}/task-{$this->id}-script.sh";

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
        return "{$this->fuseDirectory()}/task-{$this->id}.sh";
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
    private function runProcess(string $command, int $timeout = 60)
    {
        return Process::timeout($timeout)->run($command);
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

        $sshOptions = $this->buildSshOptions($this->server->privateKeyPath());

        $fullCommand = <<<SSH
            ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'bash -s' <<{$heredocToken}
            {$script}
            {$heredocToken}
        SSH;

        $this->runProcess($fullCommand, $timeout);

        $this->server->deletePrivateKey();
    }

    /**
     * Write the task's script to a temporary file.
     */
    private function writeScript(): string
    {
        $path = storage_path('app/scripts');

        if (! is_dir($path)) {
            mkdir($path, 0700, true);
        }

        $filename = 'task-'.Str::ulid().'.sh';

        $path = $path.DIRECTORY_SEPARATOR.$filename;

        file_put_contents($path, $this->script);

        return $path;
    }

    /**
     * Upload the provisioning script to the server.
     */
    protected function uploadScript(): bool
    {
        $remoteScriptPath = $this->remoteScriptPath();

        $storageScriptFile = $this->writeScript();

        $scpOptions = $this->buildScpOptions($this->server->privateKeyPath());

        $command = "scp {$scpOptions} {$storageScriptFile} {$this->user}@{$this->server->ip_address}:{$remoteScriptPath}";

        $result = $this->runProcess($command, 15);

        $this->server->deletePrivateKey();

        @unlink($storageScriptFile);

        return $result->successful();
    }

    /**
     * Run the script on the server in the background.
     */
    protected function runScriptInBackground(): void
    {
        $remoteScriptPath = $this->remoteScriptPath();

        $sshOptions = $this->buildSshOptions($this->server->privateKeyPath());

        $outputLogPath = "{$this->fuseDirectory()}/task-{$this->id}.log";

        $command = "ssh {$sshOptions} {$this->user}@{$this->server->ip_address} 'nohup bash {$remoteScriptPath} >> {$outputLogPath} 2>&1 &'";

        $this->runProcess($command);

        $this->server->deletePrivateKey();
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
