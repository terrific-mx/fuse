<?php

namespace App;

use Facades\App\ShellProcessRunner as Facade;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Process;

class ShellProcessRunner
{
    public function run($command, $timeout)
    {
        try {
            $result = Process::timeout($timeout)->run($command);
        } catch (ProcessTimedOutException $e) {
            $timedOut = true;
        }

        return new ShellResult(
            $result->exitCode(),
            $result->output(),
            $timedOut ?? false
        );
    }

    public static function mock(array $responses)
    {
        Facade::shouldReceive('run')->andReturn(...collect($responses)->flatMap(function ($response) {
            return [
                new ShellResult(0, ''), // Ensure Directory Exists
                new ShellResult(0, ''), // Upload
                $response,
            ];
        })->all());
    }
}
