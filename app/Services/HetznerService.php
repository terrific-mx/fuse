<?php

namespace App\Services;

use App\Models\ServerProvider;
use Illuminate\Support\Facades\Http;

class HetznerService
{
    /**
     * Validate Hetzner API key by attempting to fetch regions.
     */
    public function validateApiKey(string $apiKey): bool
    {
        $response = Http::withToken($apiKey)
            ->timeout(5)
            ->get('https://api.hetzner.cloud/v1/locations');

        return $response->successful() && !empty($response->json('locations'));
    }

    /**
     * Get available server types from Hetzner Cloud API.
     */
    public function getServerTypes(): array
    {
        return [
            [
                "name" => "cpx11",
                "architecture" => "x86",
                "cores" => 2,
                "cpu_type" => "shared",
                "description" => "CPX 11",
                "disk" => 40,
                "memory" => 2,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cpx21",
                "architecture" => "x86",
                "cores" => 3,
                "cpu_type" => "shared",
                "description" => "CPX 21",
                "disk" => 80,
                "memory" => 4,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cpx31",
                "architecture" => "x86",
                "cores" => 4,
                "cpu_type" => "shared",
                "description" => "CPX 31",
                "disk" => 160,
                "memory" => 8,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cpx41",
                "architecture" => "x86",
                "cores" => 8,
                "cpu_type" => "shared",
                "description" => "CPX 41",
                "disk" => 240,
                "memory" => 16,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cpx51",
                "architecture" => "x86",
                "cores" => 16,
                "cpu_type" => "shared",
                "description" => "CPX 51",
                "disk" => 360,
                "memory" => 32,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cax11",
                "architecture" => "arm",
                "cores" => 2,
                "cpu_type" => "shared",
                "description" => "CAX11",
                "disk" => 40,
                "memory" => 4,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cax21",
                "architecture" => "arm",
                "cores" => 4,
                "cpu_type" => "shared",
                "description" => "CAX21",
                "disk" => 80,
                "memory" => 8,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cax31",
                "architecture" => "arm",
                "cores" => 8,
                "cpu_type" => "shared",
                "description" => "CAX31",
                "disk" => 160,
                "memory" => 16,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cax41",
                "architecture" => "arm",
                "cores" => 16,
                "cpu_type" => "shared",
                "description" => "CAX41",
                "disk" => 320,
                "memory" => 32,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "ccx13",
                "architecture" => "x86",
                "cores" => 2,
                "cpu_type" => "dedicated",
                "description" => "CCX13 Dedicated CPU",
                "disk" => 80,
                "memory" => 8,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "ccx23",
                "architecture" => "x86",
                "cores" => 4,
                "cpu_type" => "dedicated",
                "description" => "CCX23 Dedicated CPU",
                "disk" => 160,
                "memory" => 16,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "ccx33",
                "architecture" => "x86",
                "cores" => 8,
                "cpu_type" => "dedicated",
                "description" => "CCX33 Dedicated CPU",
                "disk" => 240,
                "memory" => 32,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "ccx43",
                "architecture" => "x86",
                "cores" => 16,
                "cpu_type" => "dedicated",
                "description" => "CCX43 Dedicated CPU",
                "disk" => 360,
                "memory" => 64,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "ccx53",
                "architecture" => "x86",
                "cores" => 32,
                "cpu_type" => "dedicated",
                "description" => "CCX53 Dedicated CPU",
                "disk" => 600,
                "memory" => 128,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "ccx63",
                "architecture" => "x86",
                "cores" => 48,
                "cpu_type" => "dedicated",
                "description" => "CCX63 Dedicated CPU",
                "disk" => 960,
                "memory" => 192,
                "locations" => ["ash", "fsn1", "hel1", "hil", "nbg1", "sin"],
            ],
            [
                "name" => "cx22",
                "architecture" => "x86",
                "cores" => 2,
                "cpu_type" => "shared",
                "description" => "CX22",
                "disk" => 40,
                "memory" => 4,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cx32",
                "architecture" => "x86",
                "cores" => 4,
                "cpu_type" => "shared",
                "description" => "CX32",
                "disk" => 80,
                "memory" => 8,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cx42",
                "architecture" => "x86",
                "cores" => 8,
                "cpu_type" => "shared",
                "description" => "CX42",
                "disk" => 160,
                "memory" => 16,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
            [
                "name" => "cx52",
                "architecture" => "x86",
                "cores" => 16,
                "cpu_type" => "shared",
                "description" => "CX52",
                "disk" => 320,
                "memory" => 32,
                "locations" => ["fsn1", "hel1", "nbg1"],
            ],
        ];
    }

    /**
     * Get available Hetzner locations (hardcoded, only name and city).
     */
    public function getLocations(): array
    {
        return [
            ["name" => "fsn1", "city" => "Falkenstein"],
            ["name" => "nbg1", "city" => "Nuremberg"],
            ["name" => "hel1", "city" => "Helsinki"],
            ["name" => "ash",  "city" => "Ashburn, VA"],
            ["name" => "hil",  "city" => "Hillsboro, OR"],
            ["name" => "sin",  "city" => "Singapore"],
        ];
    }

    /**
     * Create a server on Hetzner Cloud.
     */
    public function createServer(ServerProvider $provider, string $name, string $serverType, string $location): ?array
    {
        if ($provider->provider !== 'hetzner') {
            return ['error' => 'Unsupported provider'];
        }

        $apiKey = $provider->credentials['api_key'] ?? null;
        if (! $apiKey) {
            return ['error' => 'Missing API key'];
        }

        $sshKeyId = $this->getOrCreateSshKeyId($provider);
        if (! $sshKeyId) {
            return ['error' => 'Unable to register SSH key'];
        }

        $response = Http::withToken($apiKey)
            ->post('https://api.hetzner.cloud/v1/servers', [
                'name' => $name,
                'server_type' => $serverType,
                'location' => $location,
                'image' => 'ubuntu-22.04',
                'ssh_keys' => [$sshKeyId],
            ]);

        if (! $response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            return ['error' => $error];
        }

        $server = $response->json('server');

        return [
            'provider_id' => $server['id'] ?? null,
            'ip_address' => $server['public_net']['ipv4']['ip'] ?? null,
            'status' => $server['status'] ?? null,
        ];
    }

    /**
     * Get or create the SSH key ID for the provider in Hetzner.
     */
    public function getOrCreateSshKeyId(ServerProvider $provider): ?string
    {
        if ($provider->ssh_key_id) {
            return $provider->ssh_key_id;
        }

        $apiKey = $provider->credentials['api_key'] ?? null;
        if (! $apiKey) {
            return null;
        }

        $organization = $provider->organization;
        $publicKey = $organization->ssh_public_key ?? null;
        if (! $publicKey) {
            return null;
        }

        $keyId = $this->findHetznerSshKeyId($apiKey, $publicKey);
        if (! $keyId) {
            $keyId = $this->uploadHetznerSshKey($apiKey, $publicKey, $organization->name);
        }

        if ($keyId) {
            $provider->ssh_key_id = $keyId;
            $provider->save();
        }

        return $keyId;
    }

    /**
     * Find an existing SSH key ID in Hetzner by public key.
     */
    protected function findHetznerSshKeyId(string $apiKey, string $publicKey): ?string
    {
        $response = Http::withToken($apiKey)
            ->get('https://api.hetzner.cloud/v1/ssh_keys');

        if (! $response->successful()) {
            return null;
        }

        $keys = $response->json('ssh_keys') ?? [];
        foreach ($keys as $key) {
            if (isset($key['public_key']) && trim($key['public_key']) === trim($publicKey)) {
                return $key['id'];
            }
        }

        return null;
    }

    /**
     * Upload a new SSH key to Hetzner.
     */
    protected function uploadHetznerSshKey(string $apiKey, string $publicKey, string $name): ?string
    {
        $response = Http::withToken($apiKey)
            ->post('https://api.hetzner.cloud/v1/ssh_keys', [
                'name' => $name,
                'public_key' => $publicKey,
            ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('ssh_key.id') ?? null;
    }
}
