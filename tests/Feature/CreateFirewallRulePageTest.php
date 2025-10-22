<?php

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to view the create firewall rule page for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->assertOk();
});

it('creates a firewall rule for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Allow SSH')
        ->set('action', 'allow')
        ->set('port', 22)
        ->set('from_ip_address', '192.168.1.1')
        ->call('create')
        ->assertHasNoErrors();

    $rule = $server->firewallRules()->first();

    expect($rule)->not->toBeNull();
    expect($rule->name)->toBe('Allow SSH');
    expect($rule->action)->toBe('allow');
    expect($rule->port)->toBe(22);
    expect($rule->from_ip_address)->toBe('192.168.1.1');
    expect($rule->status)->toBe('installing');
});

it('validates required fields when creating a firewall rule', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', '')
        ->set('action', '')
        ->set('port', '')
        ->call('create')
        ->assertHasErrors(['name', 'action', 'port']);
});

it('allows omitting from_ip_address when creating a firewall rule', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Allow HTTP')
        ->set('action', 'allow')
        ->set('port', 80)
        ->call('create')
        ->assertHasNoErrors();

    $rule = $server->firewallRules()->first();
    expect($rule)->not->toBeNull();
    expect($rule->from_ip_address)->toBeNull();
});

it('validates action must be allow, deny, or reject', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Invalid Action')
        ->set('action', 'block')
        ->set('port', 443)
        ->call('create')
        ->assertHasErrors(['action']);
});

it('validates port must be a valid port number', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Bad Port')
        ->set('action', 'allow')
        ->set('port', 70000)
        ->call('create')
        ->assertHasErrors(['port']);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Negative Port')
        ->set('action', 'allow')
        ->set('port', -1)
        ->call('create')
        ->assertHasErrors(['port']);
});

it('validates from_ip_address must be a valid IP address if provided', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'Bad IP')
        ->set('action', 'allow')
        ->set('port', 22)
        ->set('from_ip_address', 'not-an-ip')
        ->call('create')
        ->assertHasErrors(['from_ip_address']);
});
