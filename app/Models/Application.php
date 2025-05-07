<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

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

    protected function phpSocketPath(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->php_version) {
                'PHP 8.3' => '/run/php/php8.3-fpm.sock',
                'PHP 8.2' => '/run/php/php8.2-fpm.sock',
                'PHP 8.1' => '/run/php/php8.1-fpm.sock',
                default => throw new InvalidArgumentException('Unsupported PHP version: ' . $this->php_version),
            },
        );
    }
}
