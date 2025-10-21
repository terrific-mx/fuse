<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronjob extends Model
{
    /** @use HasFactory<\Database\Factories\CronjobFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'server_id' => 'integer',
            'status' => 'string',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Mark this cronjob as installed.
     */
    public function markAsInstalled(): void
    {
        $this->update(['status' => 'installed']);
    }

    /**
     * Mark this cronjob as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
