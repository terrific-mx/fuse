<?php

namespace App\Services;

use App\Models\ServerProvider;

class HetznerCloudClient
{
    /**
     * Create a new HetznerCloudClient instance.
     *
     * @param ServerProvider $provider
     */
    public function __construct(protected ServerProvider $provider) {}

    // TODO: Implement Hetzner Cloud API methods
}
