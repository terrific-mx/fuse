<?php

use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows an authorized user to delete a firewall rule and dispatches the uninstall job', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $rule = FirewallRule::factory()->for($server)->create(['status' => 'installed']);

    actingAs($user);

    Volt::test('servers.firewall-rules.index', ['server' => $server])
        ->call('delete', $rule->id);

    $fresh = $rule->fresh();
    expect($fresh->status)->toBe('uninstalling');

    Queue::assertPushed(\App\Jobs\UninstallFirewallRuleJob::class, function ($job) use ($rule) {
        return $job->firewallRule->is($rule);
    });
});

it('prevents unauthorized users from deleting a firewall rule', function () {
    $server = Server::factory()->create();
    $otherServer = Server::factory()->create();
    $user = $otherServer->organization->user;
    $rule = FirewallRule::factory()->for($server)->create(['status' => 'installed']);

    actingAs($user);

    Volt::test('servers.firewall-rules.index', ['server' => $otherServer])
        ->call('delete', $rule->id)
        ->assertForbidden();

    expect(FirewallRule::find($rule->id))->not()->toBeNull();
});

it('does not dispatch uninstall job or change status if firewall rule is already uninstalling', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $rule = FirewallRule::factory()->for($server)->create(['status' => 'uninstalling']);

    actingAs($user);

    Volt::test('servers.firewall-rules.index', ['server' => $server])
        ->call('delete', $rule->id);

    $fresh = $rule->fresh();
    expect($fresh->status)->toBe('uninstalling');
    Queue::assertNotPushed(\App\Jobs\UninstallFirewallRuleJob::class);
});
