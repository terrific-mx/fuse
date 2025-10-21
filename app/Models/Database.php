<?php

namespace App\Models;

use App\Jobs\UninstallDatabaseJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    /** @use HasFactory<\Database\Factories\DatabaseFactory> */
    use HasFactory;

    protected $guarded = [];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    public function install(): void
    {
        $this->update(['status' => 'installing']);
        $this->server->createInstallDatabaseTask($this)->run();
        $this->markAsInstalled();
    }

    public function markAsInstalled(): void
    {
        $this->update(['status' => 'installed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function purge(): void
    {
        if ($this->isDeleting()) {
            return;
        }

        $this->markAsDeleting();

        dispatch(new UninstallDatabaseJob($this));
    }

    public function isDeleting(): bool
    {
        return $this->status === 'deleting';
    }

    public function isInstalled(): bool
    {
        return $this->status === 'installed';
    }

    public function markAsDeleting(): void
    {
        $this->update(['status' => 'deleting']);
    }
}
