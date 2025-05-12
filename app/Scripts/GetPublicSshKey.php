<?php

namespace App\Scripts;

use App\Models\Server;

class GetPublicSshKey extends Script
{
    public $sshAs = 'fuse';

    public $name = 'Getting public SSH key';

    public function __construct(public Server $server) {}

    public function timeout()
    {
        return 30;
    }

    public function script()
    {
        return "tail -c 1M /home/{$this->server->username}/.ssh/id_rsa.pub";
    }
}
