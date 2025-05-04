<?php

namespace App;

class SecureShellCommand
{
    public static function forScript($ipAddress, $port, $keyPath, $user, $script)
    {
        return implode(' ', [
            'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no',
            '-i '.$keyPath,
            '-p '.$port,
            $user.'@'.$ipAddress,
            $script,
        ]);
    }

    public static function forUpload(
        $ipAddress,
        $port,
        $keyPath,
        $user,
        $from,
        $to
    ) {
        return sprintf(
            'scp -i %s -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o PasswordAuthentication=no -P %s %s %s:%s',
            $keyPath,
            $port,
            $from,
            $user.'@'.$ipAddress,
            $to
        );
    }
}
