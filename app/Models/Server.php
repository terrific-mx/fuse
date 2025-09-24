<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Server extends Model
{
    /** @use HasFactory<\Database\Factories\ServerFactory> */
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
            'provider_id' => 'string',
            'ip_address' => 'string',
            'status' => 'string',
        ];
    }

    /**
     * Get the organization that owns the server.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the server credential associated with the server.
     */
    public function serverCredential(): BelongsTo
    {
        return $this->belongsTo(ServerCredential::class);
    }
}
