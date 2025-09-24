<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class SshKeyGenerator
{
    /**
     * Generate a new SSH keypair for use with organizations.
     *
     * @return array{public: string, private: string}
     */
    public function generate(): array
    {
        $privateDir = storage_path('app/private');

        $random = Str::random(32);

        $keyPath = $privateDir . "/laravel_orgkey_{$random}";

        Process::run("ssh-keygen -t rsa -b 4096 -f {$keyPath} -N ''");

        $privateKey = file_get_contents($keyPath);
        $publicKey = file_get_contents("{$keyPath}.pub");

        unlink($keyPath);
        unlink("{$keyPath}.pub");

        return [
            'public' => $publicKey,
            'private' => $privateKey,
        ];
    }
}
