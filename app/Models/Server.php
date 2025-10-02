<?php

namespace App\Models;

use App\Services\OrganizationSshKeyService;
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
     * Mark this server as provisioning.
     */
    public function markProvisioning(): void
    {
        $this->update(['status' => 'provisioning']);
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
                'swapInMegabytes' => 2048,
                'swappiness' => 50,
                'server' => $this,
                'sshKeys' => collect([]),
                'mysqlMaxConnections' => 400,
                'maxChildrenPhpPool' => 14,
            ])->render(),
            'payload' => [],
            'callback' => \App\Callbacks\MarkServerProvisioned::class,
        ]);
    }

    /**
     * Provision this server by marking as provisioning, creating a task, and running it.
     */
    public function provision(): void
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
}
