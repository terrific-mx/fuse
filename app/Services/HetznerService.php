<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HetznerService
{
    /**
     * Validate Hetzner API key by attempting to fetch regions.
     *
     * @param string $apiKey
     * @return bool True if valid, false if invalid
     */
    public function validateApiKey(string $apiKey): bool
    {
        $response = Http::withToken($apiKey)
            ->timeout(5)
            ->get('https://api.hetzner.cloud/v1/locations');

        return $response->successful() && !empty($response->json('locations'));
    }
}
