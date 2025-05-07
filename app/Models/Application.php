<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'shared_directories' => 'array',
            'writeable_directories' => 'array',
            'shared_files' => 'array',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function user()
    {
        return $this->server->user;
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class)->latest();
    }

    public function sourceProvider()
    {
        return $this->belongsTo(SourceProvider::class);
    }

    public function path()
    {
        return "/home/{$this->server->username}/{$this->domain}";
    }
}
