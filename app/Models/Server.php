<?php

namespace App\Models;

use App\Callbacks\MarkServerProvisioned;
use App\Callbacks\UpdateDeploymentStatus;
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
     * The provider that this server belongs to.
     */
    public function provider()
    {
        return $this->belongsTo(ServerProvider::class, 'provider_id');
    }

    /**
     * The sites associated with this server.
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * The databases associated with this server.
     */
    public function databases()
    {
        return $this->hasMany(Database::class);
    }

    /**
     * The database users associated with this server.
     */
    public function databaseUsers()
    {
        return $this->hasMany(DatabaseUser::class);
    }

    /**
     * The cronjobs associated with this server.
     */
    public function cronjobs()
    {
        return $this->hasMany(Cronjob::class);
    }

    /**
     * The daemons associated with this server.
     */
    public function daemons()
    {
        return $this->hasMany(Daemon::class);
    }

    /**
     * The firewall rules associated with this server.
     */
    public function firewallRules()
    {
        return $this->hasMany(FirewallRule::class);
    }

    /**
     * The backups associated with this server.
     */
    public function backups()
    {
        return $this->hasMany(Backup::class);
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

        return $aptLockTask->exit_code === 0 && $aptLockTask->output === '';
    }
}
