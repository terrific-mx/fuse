<?php

namespace App\Scripts;

use App\Models\Application;

class FetchDotEnvFile extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Application $application) {}

    public function name()
    {
        return 'Fetching .env File';
    }

    public function timeout()
    {
        return 30;
    }

    public function script()
    {
        return "tail -c 1M {$this->application->path()}/shared/.env";
    }
}
