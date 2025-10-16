<?php

namespace App\Models;

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
}
