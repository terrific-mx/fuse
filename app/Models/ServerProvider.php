<?php

namespace App\Models;

use App\DigitalOcean;
use App\FakeServerProvider;
use App\ServerProviderClient;
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
        switch ($this->type) {
            case 'DigitalOcean':
                return new DigitalOcean($this);
            case 'FakeServerProvider':
                return new FakeServerProvider($this);
            default:
                return new InvalidArgumentException('Invalid server provider type.');
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
