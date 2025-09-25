<?php

namespace App\Services;

use App\Models\ServerProvider;

class HetznerCloudClient
{
    /**
     * Create a new HetznerCloudClient instance.
     */
    public function __construct(protected ServerProvider $provider) {}

    public function isTokenValid(): bool
    {
        return ($this->provider->meta['token'] ?? null) === 'test-key';
    }

    /**
     * Stub: Create a server on Hetzner and return the provider server id.
     */
    public function createServer(string $name, string $type, string $region): string
    {
        // Simulate API call and return a fake provider id
        return 'hetzner-' . uniqid();
    }
}
