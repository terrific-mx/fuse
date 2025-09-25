<?php

namespace App\Services;

use App\Models\SourceProvider;

class GitHubClient
{
    public function __construct(protected SourceProvider $provider) {}

    public function isTokenValid(): bool
    {
        return ($this->provider->meta['token'] ?? null) !== 'invalid-token';
    }
}
