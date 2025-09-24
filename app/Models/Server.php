<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'hetzner_id' => 'string',
            'ip_address' => 'string',
            'status' => 'string',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
