<?php

namespace App\Scripts;

use App\Models\Site;

class DeploySiteWithoutDowntime extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Site $site)
    {
    }

    public function name()
    {
        return 'Deploying site without downtime';
    }

    public function timeout()
    {
        return 600;
    }
}
