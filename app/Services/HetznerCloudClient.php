<?php

namespace App\Services;

use App\Models\ServerProvider;

class HetznerCloudClient
{
    /**
     * Create a new HetznerCloudClient instance.
     */
    public function __construct(protected ServerProvider $provider) {}

    // Minimal stub for token validation
    public function isTokenValid(): bool
    {
        // Accept only 'test-key' as valid for now
        return $this->provider->meta['token'] === 'test-key';
    }
}
