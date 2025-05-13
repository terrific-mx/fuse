<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\SshKey;
use App\Scripts\AddAuthorizedKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddSshKeyToServers implements ShouldQueue
{
    use Queueable;

    public function __construct(public SshKey $sshKey, public Server $server) {}

    public function handle(): void
    {
        $this->server->runInBackground(new AddAuthorizedKey($this->sshKey));
    }
}
