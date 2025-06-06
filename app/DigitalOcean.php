<?php

namespace App;

use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Support\Facades\Http;

class DigitalOcean extends FakeServerProvider implements ServerProviderClient
{
    public function __construct(public ServerProvider $serverProvider) {}

    public function valid(): bool
    {
        if ($this->request('get', '/regions')) {
            return true;
        }

        return false;
    }

    public function createServer(string $name, string $size, string $region): string
    {
        return $this->request('post', '/droplets', [
            'name' => $name,
            'size' => $size,
            'region' => $region,
            'image' => 'ubuntu-24-04-x64',
            'ipv6' => true,
            'private_networking' => true,
            'ssh_keys' => [$this->keyId()],
            'monitoring' => true,
        ])['droplet']['id'];
    }

    public function getPublicIpAddress(Server $server): ?string
    {
        return $this->getIpAddress($server);
    }

    public function regions(): array
    {
        $response = $this->request('get', '/regions');

        if (! $response) {
            return [];
        }

        $regions = $response['regions'] ?? [];

        return collect($regions)
            ->filter(function ($region) {
                return $region['available'];
            })
            ->mapWithKeys(function ($region) {
                return [$region['slug'] => $region['name']];
            })->all();
    }

    public function sizes(string $region): array
    {
        $response = $this->request('get', '/sizes');

        if (! $response) {
            return [];
        }

        $sizes = $response['sizes'] ?? [];

        return collect($sizes)
            ->filter(function ($size) use ($region) {
                return in_array($region, $size['regions']) && $size['available'];
            })
            ->mapWithKeys(function ($size) {
                return [$size['slug'] => $size['slug']];
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
            return $this->request('get', '/account/keys/'.$id)['ssh_key'];
        }

        return collect($this->aggregate('get', '/account/keys', 'ssh_keys'))->first(function ($key) {
            return $key['public_key'] == trim($this->serverProvider->user->public_key);
        });
    }

    protected function addKey()
    {
        return $this->request('post', '/account/keys', [
            'name' => 'Fuse',
            'public_key' => $this->serverProvider->user->public_key,
        ])['ssh_key']['id'];
    }

    protected function getIpAddress(Server $server, $type = 'public'): ?string
    {
        $networks = $this->request(
            'get', "/droplets/{$server->provider_server_id}"
        )['droplet']['networks']['v4'] ?? [];

        return collect($networks)->filter(function ($network) use ($type) {
            return ($network['type'] ?? null) == $type;
        })->first()['ip_address'] ?? null;
    }

    protected function request(string $method, string $path, $parameters = [])
    {
        /** @var \Illuminate\Http\Client\Response */
        $response = Http::withToken($this->serverProvider->token)
            ->{$method}('https://api.digitalocean.com/v2/'.ltrim($path, '/'), $parameters);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    protected function aggregate(string $method, string $path, string $target, $parameters = [])
    {
        $page = 1;

        $results = [];

        do {
            $response = $this->request(
                $method, $path.'?page='.$page.'&per_page=100', $parameters
            );

            $results = array_merge($results, $response[$target]);

            $page++;
        } while (isset($response['links']['pages']['next']));

        return $results;
    }
}
