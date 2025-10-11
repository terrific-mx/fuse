<?php

namespace App\Models;

use App\Jobs\InstallCaddyFileJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
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
            'output' => 'string',
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
     * Check if the deployment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
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

    /**
     * Mark the deployment as deployed and handle post-deploy actions.
     */
    public function markDeployed(?string $output = null)
    {
        tap($this)->update([
            'status' => 'deployed',
            'output' => $output,
        ]);
    }

    /**
     * Finalize the deployment: mark as deployed, update commit hash from git.
     *
     * @return void
     */
    public function finalizeDeployment(?string $output = null): void
    {
        $this->markDeployed($output);

        if (! $this->site->isCaddyInstalled()) {
            InstallCaddyFileJob::dispatch($this->site);
        };

        $task = $this->site->server->createGetGitHashTask($this->site)->run();

        $this->update(['commit' => $task->output]);
    }

    /**
     * Get the short (7-char) commit hash.
     */
    protected function shortCommit(): Attribute
    {
        return Attribute::get(fn () => $this->commit ? Str::substr($this->commit, 0, 7) : null);
    }

    /**
     * Mark the deployment as failed.
     */
    public function markFailed(?string $output = null): void
    {
        $this->update([
            'status' => 'failed',
            'output' => $output,
        ]);
    }
}
