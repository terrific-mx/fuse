<?php

namespace App\Scripts;

use App\Models\Server;

class UpdateCaddyImports extends Script
{
    /**
     * Create a new class instance.
     */
    public function __construct(public Server $server) {}

    public function name()
    {
        return 'Updating Caddy Imports';
    }

    public function script()
    {
        return view('scripts.server.update-caddy-imports', [
            'applications' => $this->server->applications()
                ->with('server')
                ->where('status', 'installed')
                ->lazy(chunkSize: 100),
        ]);
    }
}
