<?php

use App\Models\Server;
use Livewire\Volt\Volt;

// TDD: users can add a firewall rule to a server

test('users can add a firewall rule to a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.firewall-rules', ['server' => $server])
        ->set('form.name', 'SSH')
        ->set('form.action', 'allow')
        ->set('form.port', 22)
        ->set('form.from_ip', '0.0.0.0/0')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->firewallRules)->toHaveCount(1);

    $rule = $server->firewallRules()->first();
    expect($rule->name)->toBe('SSH');
    expect($rule->action)->toBe('allow');
    expect($rule->port)->toBe(22);
    expect($rule->from_ip)->toBe('0.0.0.0/0');
});
