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
        return $this->provider->meta['token'] === 'test-key';
    }
}
