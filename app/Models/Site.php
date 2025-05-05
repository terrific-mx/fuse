<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function user()
    {
        return $this->server->user;
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
