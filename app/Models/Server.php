<?php

namespace App\Models;

use App\Callbacks\MarkServerProvisioned;
use App\Callbacks\UpdateDeploymentStatus;
use App\Jobs\InstallCleanupCronJob;
use App\Jobs\RetrieveRemoteSshKey;
use App\Services\OrganizationSshKeyService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
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
            'sudo_password' => 'encrypted',
            'database_password' => 'encrypted',
        ];
    }

    /**
     * The organization that this server belongs to.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The user who created this server.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The SSH keys associated with this server.
     */
    public function sshKeys()
    {
        return $this->belongsToMany(SshKey::class, 'server_ssh_key');
    }

    /**
     * The sites associated with this server.
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * The daemons associated with this server.
     */
    public function daemons()
    {
        return $this->hasMany(Daemon::class);
    }

    /**
     * The databases associated with this server.
     */
    public function databases()
    {
        return $this->hasMany(Database::class);
    }

    /**
     * The firewall rules associated with this server.
     */
    public function firewallRules()
    {
        return $this->hasMany(FirewallRule::class);
    }

    /**
     * Create and run a task to install the cleanup cron.
     */
    public function createInstallCleanupCronTask(): Task
    {
        return $this->tasks()->create([
            'name' => 'install_cleanup_cron',
            'user' => 'root',
            'script' => view('scripts.server.install-cleanup-cron')->render(),
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * The tasks associated with this server.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Create a deploy task for the given deployment.
     */
    public function createDeployTask(Deployment $deployment): Task
    {
        return $this->tasks()->create([
            'name' => 'deploy',
            'status' => 'pending',
            'user' => 'fuse',
            'script' => view('scripts.site.deploy', [
                'server' => $this,
                'site' => $deployment->site,
                'deployment' => $deployment,
            ])->render(),
            'after_actions' => [
                (new UpdateDeploymentStatus($deployment->id))->toCallbackArray(),
            ],
        ]);
    }

    /**
     * Create a get_git_hash task for the given site.
     */
    public function createGetGitHashTask(Site $site): Task
    {
        return $this->tasks()->create([
            'name' => 'get_git_hash',
            'user' => 'fuse',
            'script' => <<<EOT
                cd {$site->repository_directory}

                git rev-list {$site->repository_branch} -1
                EOT,
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Create a get_env_file task for the given site.
     */
    public function createGetEnvFileTask(Site $site)
    {
        $envPath = $site->path.'/shared/.env';

        return $this->tasks()->create([
            'name' => 'get_env_file',
            'user' => 'fuse',
            'script' => "cat {$envPath}",
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Create a set_env_file task for the given site and content.
     */
    public function createSetEnvFileTask(Site $site, string $content)
    {
        $directory = $site->path.'/shared';
        $path = $site->path.'/shared/.env';
        $script = view('scripts.site.set_env_file', [
            'directory' => $directory,
            'path' => $path,
            'contents' => $content,
        ])->render();

        return $this->tasks()->create([
            'name' => 'set_env_file',
            'user' => 'fuse',
            'script' => $script,
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Mark this server as provisioning.
     */
    public function markProvisioning(): void
    {
        $this->update(['status' => 'provisioning']);
    }

    /**
     * Mark this server as provisioned.
     */
    public function markProvisioned(): void
    {
        $this->update(['status' => 'provisioned']);
    }

    /**
     * Run all post-provisioning actions for this server.
     */
    public function afterProvisioned(): void
    {
        $this->markProvisioned();

        $this->firewall();

        dispatch(new RetrieveRemoteSshKey($this));
        dispatch(new InstallCleanupCronJob($this));
    }

    /**
     * Create the default firewall rules for SSH, HTTP, and HTTPS.
     */
    public function firewall(): void
    {
        $this->firewallRules()->createMany([
            ['port' => 22, 'status' => 'installed'],
            ['port' => 80, 'status' => 'installed'],
            ['port' => 443, 'status' => 'installed'],
        ]);
    }

    /**
     * Create and run a get_ssh_key task, and update the public_ssh_key.
     */
    public function syncPublicSshKey(): void
    {
        $task = $this->tasks()->create([
            'name' => 'get_ssh_key',
            'user' => 'fuse',
            'script' => 'cat ~/.ssh/id_rsa.pub',
            'payload' => [],
            'after_actions' => [],
        ]);

        $task = $task->run();

        $this->update(['public_ssh_key' => trim($task->output)]);
    }

    /**
     * Create a provisioning task for this server.
     */
    public function createProvisionTask(): Task
    {
        return $this->tasks()->create([
            'name' => 'provision',
            'user' => 'root',
            'script' => view('scripts.server.provision', [
                'server' => $this,
                'swapInMegabytes' => 2048,
                'swappiness' => 50,
                'mysqlMaxConnections' => 400,
                'maxChildrenPhpPool' => 14,
            ])->render(),
            'payload' => [],
            'after_actions' => [
                (new MarkServerProvisioned)->toCallbackArray(),
            ],
        ]);
    }

    /**
     * Provision this server by marking as provisioning, creating a task, and running it.
     */
    public function provision()
    {
        $this->markProvisioning();

        $task = $this->createProvisionTask();

        $task->provision();
    }

    /**
     * Write the organization's private key to storage and return the path.
     */
    public function privateKeyPath(): string
    {
        $sshKeyService = app(OrganizationSshKeyService::class);

        return $sshKeyService->writePrivateKeyToStorage($this->organization);
    }

    /**
     * Delete the organization's private key from storage.
     */
    public function deletePrivateKey(): void
    {
        $sshKeyService = app(OrganizationSshKeyService::class);

        $sshKeyService->deletePrivateKeyFromStorage($this->organization);
    }

    /**
     * Get the color representing the server status.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->status) {
                'active', 'provisioned' => 'green',
                'provisioning' => 'blue',
                'failed', 'error' => 'red',
                default => 'gray',
            };
        });
    }

    /**
     * Get the status with the first letter uppercased.
     */
    protected function statusFormatted(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->status));
    }

    /**
     * Check if the server status is provisioning.
     */
    protected function isProvisioning(): Attribute
    {
        return Attribute::get(fn () => $this->status === 'provisioning');
    }

    /**
     * Check if the server status is provisioned.
     */
    protected function isProvisioned(): Attribute
    {
        return Attribute::get(fn () => $this->status === 'provisioned');
    }

    /**
     * Check if the server is older than the given number of minutes.
     */
    public function isOlderThanMinutes(int $minutes): bool
    {
        return $this->created_at->lt(now()->subMinutes($minutes));
    }

    /**
     * Determine if the server is ready for provisioning by running readiness checks.
     */
    public function isReadyForProvisioning()
    {
        $pwdTask = $this->tasks()->create([
            'name' => 'provisioning-readiness-pwd',
            'user' => 'root',
            'script' => 'pwd',
            'payload' => [],
            'after_actions' => [],
        ])->run();

        if ($pwdTask->output !== '/root') {
            return false;
        }

        $aptLockScript = 'lsof | grep /var/lib/dpkg/lock && ps -e | grep -e apt -e adept | grep -v grep';

        $aptLockTask = $this->tasks()->create([
            'name' => 'provisioning-readiness-apt-lock',
            'user' => 'root',
            'script' => $aptLockScript,
            'payload' => [],
            'after_actions' => [],
        ])->run();

        // Expect exit_code === 1 because the readiness check script returns 1 when no apt/dpkg lock or apt process is found (system is ready for provisioning)
        return $aptLockTask->exit_code === 1 && $aptLockTask->output === '';
    }

    /**
     * Create an install_daemon task for the given daemon.
     */
    public function createInstallDaemonTask(Daemon $daemon)
    {
        $conf = view('scripts.daemon.supervisor-conf', [
            'daemon' => $daemon,
        ])->render();

        $sh = <<<BASH
            cat <<'EOF' > {$daemon->config_path}
            {$conf}
            EOF
            supervisorctl reread
            supervisorctl update
            BASH;

        return $this->tasks()->create([
            'name' => 'install_daemon',
            'user' => 'root',
            'script' => $sh,
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Create an install_database task for the given database.
     */
    public function createInstallDatabaseTask(Database $database)
    {
        $sh = <<<BASH
            MYSQL_PWD="{$this->database_password}" mysql -u fuse -e "CREATE DATABASE IF NOT EXISTS {$database->name};"
            BASH;

        return $this->tasks()->create([
            'name' => 'install_database',
            'user' => 'root',
            'script' => $sh,
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Create an authorize_ssh_key task for the given SSH key.
     */
    public function createAuthorizeSshKeyTask(SshKey $sshKey)
    {
        $script = <<<BASH
            echo '{$sshKey->public_key}' >> ~/.ssh/authorized_keys
            BASH;

        return $this->tasks()->create([
            'name' => 'authorize_ssh_key',
            'user' => 'fuse',
            'script' => $script,
            'payload' => [],
            'after_actions' => [],
        ]);
    }

    /**
     * Create a deauthorize_ssh_key task for the given SSH key.
     */
    public function createDeauthorizeSshKeyTask(SshKey $sshKey)
    {
        $escapedKey = str_replace('/', '\/', $sshKey->public_key);
        $script = <<<BASH
            sed -i.bak '/{$escapedKey}/d' ~/.ssh/authorized_keys
            BASH;

        return $this->tasks()->create([
            'name' => 'deauthorize_ssh_key',
            'user' => 'fuse',
            'script' => $script,
            'payload' => [],
            'after_actions' => [],
        ]);
    }
}
