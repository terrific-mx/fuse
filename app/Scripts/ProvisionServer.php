<?php

namespace App\Scripts;

use App\Models\Server;

class ProvisionServer extends Script
{
    public $name = 'Provisioning Server';

    public function __construct(public Server $server) {}

    public function script()
    {
        return view('scripts.server.provision', [
            'script' => $this,
            'server' => $this->server,
            'user' => $this->server->user,
            'sshKeys' => $this->server->user->sshKeys,
            'swapInMegabytes' => 1024,
            'swappiness' => 1024,
            'mysqlMaxConnections' => 100,
            'maxChildrenPhpPool' => 5,
        ])->render();
    }
}
