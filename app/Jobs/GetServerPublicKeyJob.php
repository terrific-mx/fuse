<?php

namespace App\Jobs;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

class GetServerPublicKeyJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Server $server) {}

    public function handle(): void
    {
        // SSH as fuse user to retrieve the public key
        $ip = $this->server->ip_address;
        $process = Process::run(
            "ssh -o StrictHostKeyChecking=no fuse@{$ip} 'cat ~/.ssh/id_rsa.pub'"
        );

        if ($process->successful()) {
            $this->server->update([
                'public_key' => trim($process->output()),
            ]);
        }
    }
}
