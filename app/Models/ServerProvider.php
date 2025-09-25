<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class ServerProvider extends Model
{
    /** @use HasFactory<\Database\Factories\ServerProviderFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'ssh_key_id' => 'string',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the masked API key.
     */
    protected function maskedApiKey(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->credentials['api_key'])
                ? Str::mask($this->credentials['api_key'], '*', 4, max(0, strlen($this->credentials['api_key']) - 8))
                : __('(none)')
        );
    }
}
