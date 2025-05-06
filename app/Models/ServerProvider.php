<?php

namespace App\Models;

use App\DigitalOcean;
use App\FakeServerProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ServerProvider extends Model
{
    /** @use HasFactory<\Database\Factories\ServerProviderFactory> */
    use HasFactory;

    protected function casts()
    {
        return [
            'token' => 'encrypted',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return match ($this->type) {
            'DigitalOcean' => new DigitalOcean($this),
            'FakeServerProvider' => new FakeServerProvider($this),
            default => throw new InvalidArgumentException('Invalid server provider type.'),
        };
    }

    public function validRegion($size)
    {
        return array_key_exists($size, $this->regions());
    }

    public function regions()
    {
        return $this->client()->regions();
    }

    public function validSize($size)
    {
        return array_key_exists($size, $this->sizes());
    }

    public function sizes()
    {
        return $this->client()->sizes();
    }
}
