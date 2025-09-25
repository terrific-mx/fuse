<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerProvider extends Model
{
    /** @use HasFactory<\Database\Factories\ServerProviderFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
