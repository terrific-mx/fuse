<?php

namespace App\Models;

use App\Jobs\ProvisionServer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Server extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServerProvider::class, 'provider_id');
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function databases()
    {
        return $this->hasMany(Database::class);
    }

    public function databaseUsers()
    {
        return $this->hasMany(DatabaseUser::class);
    }

    public function cronjobs()
    {
        return $this->hasMany(Cronjob::class);
    }

    public function daemons()
    {
        return $this->hasMany(Daemon::class);
    }

    public function firewallRules()
    {
        return $this->hasMany(FirewallRule::class);
    }

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

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
        $this->markProvisioning();

        $task = $this->createProvisionTask();

        $task->provision();
    }
}
