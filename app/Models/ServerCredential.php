<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerCredential extends Model
{
    /** @use HasFactory<\Database\Factories\ServerCredentialFactory> */
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
            'credentials' => 'encrypted:array',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
