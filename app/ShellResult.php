<?php

namespace App;

class ShellResult
{
    /**
     * Create a new class instance.
     */
    public function __construct(public int $exitCode, public string $output, public bool $timedOut = false)
    {
        //
    }
}
