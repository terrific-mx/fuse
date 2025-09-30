<?php

namespace App\Models;

use App\Jobs\ProvisionServer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return [];
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
            'script' => 'provision.sh',
            'payload' => [],
            'callback' => \App\Callbacks\MarkServerProvisioned::class,
        ]);
    }

    /**
     * Provision this server by marking as provisioning, creating a task, and running it.
     */
    public function provision(): void
    {
        dd('sdf');
        $this->markProvisioning();

        $task = $this->createProvisionTask();

        $task->provision();
    }
}
