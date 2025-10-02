<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'shared_files' => 'array',
            'writeable_directories' => 'array',
            'shared_directories' => 'array',
            'script_before_deploy' => 'string',
            'script_after_deploy' => 'string',
            'script_before_activate' => 'string',
            'script_after_activate' => 'string',
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


}
