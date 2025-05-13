<?php

namespace App\Scripts;

use App\Models\SshKey;

class AddAuthorizedKey extends Script
{
    public $sshAs = 'fuse';

    public $name = 'Adding authorized key';

    public function __construct(public SshKey $sshKey)
    {
    }

    public function timeout()
    {
        return 15;
    }
}
