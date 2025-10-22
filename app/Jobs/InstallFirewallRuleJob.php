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
        $rule = $this->firewallRule;
        $server = $rule->server;

        // Create the install_firewall_rule task on the server
        $task = $server->createInstallFirewallRuleTask($rule);

        // Simulate running the task
        $result = \Illuminate\Support\Facades\Process::run($task->script);

        // If the process succeeded, mark as installed
        if ($result->successful()) {
            $rule->markAsInstalled();
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->firewallRule->markAsFailed();
    }
}
