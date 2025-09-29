<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'databases' => 'array',
            'directories' => 'array',
            'notify_failure' => 'boolean',
            'notify_success' => 'boolean',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
