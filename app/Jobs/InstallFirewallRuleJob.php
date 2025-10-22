<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallFirewallRuleJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public \App\Models\FirewallRule $firewallRule
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = $this->firewallRule->server->createInstallFirewallRuleTask($this->firewallRule)->run();

        if (! $task->isSuccessful()) {
            $this->fail(new \Exception('Failed to install firewall rule'));

            return;
        }

        $this->firewallRule->markAsInstalled();
    }

    public function failed(\Throwable $exception): void
    {
        $this->firewallRule->markAsFailed();
    }
}
