<?php

namespace App\Models;

use App\Jobs\InstallFirewallRuleJob;
use App\Jobs\UninstallFirewallRuleJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallRule extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    public $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'status' => 'string',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Mark this firewall rule as installing and dispatch the install job.
     */
    public function install(): void
    {
        if ($this->isInstalling()) {
            return;
        }

        $this->update(['status' => 'installing']);

        InstallFirewallRuleJob::dispatch($this);
    }

    /**
     * Mark this firewall rule as uninstalling and dispatch the uninstall job.
     */
    public function uninstall(): void
    {
        if ($this->isUninstalling()) {
            return;
        }

        $this->update(['status' => 'uninstalling']);

        UninstallFirewallRuleJob::dispatch($this);
    }

    /**
     * Determine if the firewall rule is currently uninstalling.
     */
    public function isUninstalling(): bool
    {
        return $this->status === 'uninstalling';
    }

    /**
     * Determine if the firewall rule is installed.
     */
    public function isInstalled(): bool
    {
        return $this->status === 'installed';
    }

    /**
     * Determine if the firewall rule is currently installing.
     */
    public function isInstalling(): bool
    {
        return $this->status === 'installing';
    }

    public function markAsInstalled(): void
    {
        $this->update(['status' => 'installed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
