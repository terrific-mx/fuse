<?php

namespace App\Models;

use App\FakeSourceProvider;
use App\Github;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class SourceProvider extends Model
{
    /** @use HasFactory<\Database\Factories\SourceProviderFactory> */
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
            'GitHub' => new GitHub($this),
            'FakeSourceProvider' => new FakeSourceProvider($this),
            default => throw new InvalidArgumentException('Invalid source provider type.'),
        };
    }
}
