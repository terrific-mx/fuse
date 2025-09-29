<?php

namespace App\Callbacks;

use App\Models\Server;

class MarkServerProvisioned
{
    /**
     * Mark the given server as provisioned.
     */
    public function __invoke(Server $server): Server
    {
        $server->update(['status' => 'provisioned']);

        return $server;
    }
}
