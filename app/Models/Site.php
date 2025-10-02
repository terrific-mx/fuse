<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Encryption\Encrypter;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'shared_files' => 'array',
            'writable_directories' => 'array',
            'shared_directories' => 'array',
            'script_before_deploy' => 'string',
            'script_after_deploy' => 'string',
            'script_before_activate' => 'string',
            'script_after_activate' => 'string',
            'installed_at' => 'datetime',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Get the PHP binary location for the site based on its PHP version.
     */
    protected function phpBinary(): Attribute
    {
        return Attribute::make(
            get: fn () => "/usr/bin/php{$this->php_version}"
        );
    }

    /**
     * Get the site path based on the hostname.
     */
    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn () => "/home/fuse/{$this->hostname}"
        );
    }

    /**
     * Get the repository directory for the site.
     */
    protected function repositoryDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->path}/repository"
        );
    }

    /**
     * Get the shared directory for the site.
     */
    protected function sharedDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->path}/shared"
        );
    }

    /**
     * Get the releases directory for the site.
     */
    protected function releasesDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->path}/releases"
        );
    }

    /**
     * Get the logs directory for the site.
     */
    protected function logsDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->path}/logs"
        );
    }

    /**
     * Get the number of releases to retain for this site.
     */
    protected function releasesRetention(): Attribute
    {
        return Attribute::make(
            get: fn () => 5
        );
    }

    /**
     * Get the current directory for the site.
     */
    protected function currentDirectory(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->path}/current"
        );
    }

    public function latestFinishedDeployment(): HasOne
    {
        return $this->hasOne(Deployment::class)->latestOfMany()->where('status', 'finished');
    }

    /**
     * Get the auto-generated environment variables for the site.
     */
    protected function environmentVariables(): Attribute
    {
        return Attribute::make(
            get: function () {
                return [
                    'APP_KEY' => 'base64:' . base64_encode(Encrypter::generateKey('AES-256-CBC')),
                    'APP_URL' => "https://{$this->hostname}",
                ];
            }
        );
    }
}
