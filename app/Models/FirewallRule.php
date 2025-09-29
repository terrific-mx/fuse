<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallRule extends Model
{
    /** @use HasFactory<\Database\Factories\FirewallRuleFactory> */
    use HasFactory;

    protected $guarded = [];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}

