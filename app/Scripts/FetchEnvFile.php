<?php

namespace App\Scripts;

use App\Models\Application;

class FetchEnvFile extends Script
{
    public $sshAs = 'fuse';

    /**
     * Create a new class instance.
     */
    public function __construct(public Application $application)
    {
    }

    public function name()
    {
        return 'Fetching .env File';
    }

    public function script()
    {
        return "tail -c 1M {$this->application->path()}/shared/.env";
    }
}
