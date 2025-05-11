<?php

namespace App;

use App\Models\Server;

interface ServerProviderClient
{
    public function valid(): bool;

    public function createServer(string $name, string $size, string $region): string;

    public function sizes(string $region): array;

    public function regions(): array;

    public function getPublicIpAddress(Server $server): ?string;
}
