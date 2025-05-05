<?php

namespace App\Scripts;

class DeleteFolder extends Script
{
    public function __construct(public string $path)
    {

    }

    public function timeout()
    {
        return 30;
    }

    public function name()
    {
        return 'Deleting folder';
    }

    public function script()
    {
        return view('scripts.server.delete-folder', ['path' => $this->path])->render();
    }
}
