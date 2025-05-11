<?php

namespace App;

use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Support\Str;

class FakeServerProvider implements ServerProviderClient
{
    public function __construct(public ServerProvider $serverProvider) {}

    public function valid(): bool
    {
        if ($this->serverProvider->token === 'valid-token') {
            return true;
        }

        return false;
    }

    public function createServer(string $name, string $size, string $region): string
    {
        $sshKey = $this->keyId();

        return Str::random(10);
    }

    protected function keyId()
    {
        return tap(Str::random(10), function ($id) {
            $this->serverProvider->update([
                'provider_key_id' => $id,
            ]);
        });
    }

    public function sizes(string $region): array
    {
        return [
            's-1vcpu-512mb-10gb' => 's-1vcpu-512mb-10gb',
        ];
    }

    public function regions(): array
    {
        return [
            'nyc1' => 'New York 1',
            'sfo3' => 'San Francisco 3',
        ];
    }

    public function getPublicIpAddress(Server $server): ?string
    {
        if ($server->name === 'server-that-does-not-return-an-ip-address') {
            return null;
        }

        return '192.168.1.1';
    }
}
