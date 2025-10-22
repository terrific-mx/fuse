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
        //
    }
}
