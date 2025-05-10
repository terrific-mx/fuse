<?php

namespace App\Scripts;

use App\Models\Application;

class SaveDotEnvFile extends Script
{
    public $sshAs = 'fuse';

    /**
     * Create a new class instance.
     */
    public function __construct(public Application $application, public string $content)
    {
    }

    public function name()
    {
        return 'Saving .env File';
    }

    public function timeout()
    {
        return 20;
    }

    public function script()
    {
        return view('scripts.application.save-dot-env-file', [
            'path' => "{$this->application->path()}/shared/.env",
            'content' => $this->content,
        ])->render();
    }
}
