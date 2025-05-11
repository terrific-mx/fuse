<?php

namespace App;

use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HetznerCloud extends FakeServerProvider implements ServerProviderClient
{
    public function __construct(public ServerProvider $serverProvider) {}

    public function valid(): bool
    {
        return (bool) $this->request('get', '/datacenters')->successful();
    }

    public function createServer(string $name, string $size, string $region): string
    {
        $response = $this->request('post', '/servers', [
            'name' => $name,
            'server_type' => $size,
            'location' => $region,
            'image' => 'ubuntu-24.04',
            'ssh_keys' => [$this->keyId()],
            'start_after_create' => true,
        ]);

        if ($response->successful()) {
            return $response->object()->server->id;
        }

        if ($response->clientError()) {
            new RuntimeException($response->object()->error->message);
        }

        return '';
    }

    public function getPublicIpAddress(Server $server): ?string
    {
        return $this->getIpAddress($server);
    }

    public function regions(): array
    {
        $response = $this->request('get', '/locations');

        if (!$response->successful()) {
            return [];
        }

        $locations = $response->object()->locations ?? [];

        return collect($locations)
            ->mapWithKeys(function ($locations) {
                return [$locations->name => $locations->description];
            })->all();
    }

    public function sizes(string $region): array
    {
        $response = $this->request('get', '/server_types');

        if (!$response->successful()) {
            return [];
        }

        $serverTypes = $response->object()->server_types ?? [];

        return collect($serverTypes)
            ->filter(function ($type) use ($region) {
                if (!empty($type->deprecation)) {
                    return false;
                }
                $availableLocations = array_column($type->prices ?? [], 'location');
                return in_array($region, $availableLocations);
            })->mapWithKeys(function ($type) {
                return [$type->name => $type->description];
            })->all();
    }

    protected function keyId()
    {
        return tap($this->findKey()['id'] ?? $this->addKey(), function ($id) {
            $this->serverProvider->update([
                'provider_key_id' => $id,
            ]);
        });
    }

    protected function findKey()
    {
        if ($id = $this->serverProvider->provider_key_id) {
            return $this->request('get', '/ssh_keys/'.$id)['ssh_key'];
        }

        return collect($this->request('get', '/ssh_keys')['ssh_keys'])->first(function ($key) {
            return $key['public_key'] == trim($this->serverProvider->user->public_key);
        });
    }

    protected function addKey()
    {
        return $this->request('post', '/ssh_keys', [
            'name' => 'Fuse',
            'public_key' => $this->serverProvider->user->public_key,
        ])['ssh_key']['id'];
    }

    protected function getIpAddress(Server $server): ?string
    {
        $serverData = $this->request(
            'get', "/servers/{$server->provider_server_id}"
        )['server'] ?? [];

        return $serverData['public_net']['ipv4']['ip'] ?? null;
    }

    protected function request(string $method, string $path, $parameters = [])
    {
        /** @var \Illuminate\Http\Client\Response */
        $response = Http::withToken($this->serverProvider->token)
            ->{$method}('https://api.hetzner.cloud/v1/'.ltrim($path, '/'), $parameters);

        return $response;
    }
}
