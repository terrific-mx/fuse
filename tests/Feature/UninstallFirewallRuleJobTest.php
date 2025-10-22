<?php

use App\Jobs\UninstallFirewallRuleJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an uninstall_firewall_rule task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'firewall rule uninstalled')), // Execute script
    ]);

    $server = Server::factory()->create();
    $firewallRule = FirewallRule::factory()
        ->for($server)
        ->installed()
        ->create();

    $job = new UninstallFirewallRuleJob($firewallRule);
    $job->handle();

    expect(FirewallRule::find($firewallRule->id))->toBeNull();

    $task = $server->tasks()
        ->where('name', 'uninstall_firewall_rule')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain('ufw delete');

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('does not delete the firewall rule if the uninstall task fails', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(exitCode: 1, output: 'failed to remove rule')),
    ]);

    $server = Server::factory()->create();
    $firewallRule = FirewallRule::factory()
        ->for($server)
        ->installed()
        ->create();

    $job = new UninstallFirewallRuleJob($firewallRule);
    $job->handle();

    expect(FirewallRule::find($firewallRule->id))->not()->toBeNull();
    expect($firewallRule->fresh()->status)->toBe('installed');
});

it('marks the firewall rule as installed when the job fails', function () {
    $firewallRule = FirewallRule::factory()->installed()->create();

    $job = new UninstallFirewallRuleJob($firewallRule);
    $job->failed(new \Exception('Simulated failure'));

    expect($firewallRule->fresh()->status)->toBe('installed');
});
