<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    /** @use HasFactory<\Database\Factories\DeploymentFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Get the release directory for this deployment.
     */
    protected function releaseDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->site->releases_directory}/{$this->created_at->timestamp}"
        );
    }

    /**
     * Get the color representing the deployment status.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::get(function () {
            return match (
                $this->status
            ) {
                'success', 'deployed' => 'green',
                'failed' => 'red',
                default => 'amber',
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
     * Check if the deployment status is pending.
     */
    protected function isPending(): Attribute
    {
        return Attribute::get(fn () => $this->status === 'pending');
    }

    /**
     * Check if the deployment is deployed.
     */
    public function isDeployed(): bool
    {
        return $this->status === 'deployed';
    }

    /**
     * Check if the deployment is currently deploying.
     */
    public function isDeploying(): bool
    {
        return $this->status === 'deploying';
    }

    /**
     * Check if the deployment is stale (older than 10 minutes).
     */
    public function isStale(): bool
    {
        return $this->created_at->lt(now()->subMinutes(10));
    }

    /**
     * Mark the deployment as deploying.
     */
    public function markDeploying(): void
    {
        $this->update(['status' => 'deploying']);
    }

    /**
     * Deploy this deployment: mark as deploying, create and provision the deploy task.
     */
    public function deploy(): void
    {
        $this->markDeploying();

        $server = $this->site->server;

        $task = $server->createDeployTask($this);

        $task->provision();
    }
}
