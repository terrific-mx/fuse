<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SshKey extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class, 'server_ssh_key');
    }

    /**
     * Get the masked public key (first 10 and last 10 chars visible).
     */
    protected function maskedPublicKey(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => (
                strlen($attributes['public_key']) <= 20
                    ? $attributes['public_key']
                    : Str::mask($attributes['public_key'], '*', 10, max(0, strlen($attributes['public_key']) - 20))
            ),
        );
    }
}
