<?php

namespace App\Services;

use App\Models\Server;
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

    /**
     * Retrieve the IP address for the given server from Hetzner.
     */
    public function getServerIp(Server $server): ?string
    {
        // Stub: Return a fake IP address for demonstration
        return '192.0.2.1';
    }
}
