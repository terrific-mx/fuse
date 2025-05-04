<?php

namespace App\Models;

use App\FakeSourceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return new FakeSourceProvider($this);
    }
}
