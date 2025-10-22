<?php

namespace App\Jobs;

use App\Models\FirewallRule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UninstallFirewallRuleJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FirewallRule $firewallRule
    ) {}

    public function handle(): void
    {
        $task = $this->firewallRule->server->createUninstallFirewallRuleTask($this->firewallRule)->run();

        if (! $task->isSuccessful()) {
            $this->fail(new \Exception('Uninstall firewall rule task failed'));

            return;
        }

        $this->firewallRule->delete();
    }

    public function failed(\Throwable $exception): void
    {
        $this->firewallRule->markAsInstalled();
    }
}
