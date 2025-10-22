<?php

use App\Jobs\InstallFirewallRuleJob;
use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Support\Facades\Process;

it('creates and runs an install_firewall_rule task for the server', function () {
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'firewall rule installed')), // Execute script
    ]);

    $server = Server::factory()->create();
    $rule = FirewallRule::factory()
        ->for($server)
        ->pending()
        ->create([
            'name' => 'Allow SSH',
            'action' => 'allow',
            'port' => 22,
            'from_ip_address' => '192.168.1.1',
        ]);

    $job = new InstallFirewallRuleJob($rule);
    $job->handle();

    expect($rule->fresh()->status)->toBe('installed');

    $task = $server->tasks()
        ->where('name', 'install_firewall_rule')
        ->where('user', 'root')
        ->first();

    expect($task)->not()->toBeNull();
    expect($task->script)->toContain((string) $rule->port);

    Process::assertRan(function ($process, $result) {
        return true;
    });
});

it('sets status to failed when the job fails', function () {
    $rule = FirewallRule::factory()->pending()->create();

    $job = new InstallFirewallRuleJob($rule);
    $job->failed(new \Exception('Simulated failure'));

    expect($rule->fresh()->status)->toBe('failed');
});
