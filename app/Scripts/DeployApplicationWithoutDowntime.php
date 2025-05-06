<?php

namespace App\Scripts;

use App\Models\Application;

class DeployApplicationWithoutDowntime extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Application $application)
    {
    }

    public function name()
    {
        return 'Deploying application without downtime';
    }

    public function timeout()
    {
        return 600;
    }
}
