<?php

namespace App\Scripts;

use App\Models\Task;

class Script
{
    public $sshAs = 'root';

    protected static $token;

    public function name()
    {
        return $this->name ?? '';
    }

    public function timeout()
    {
        return Task::DEFAULT_TIMEOUT;
    }

    public function script()
    {
        return '';
    }

    public function __toString()
    {
        return $this->script();
    }
}
