<?php

use App\Models\User;
use App\Models\ServerProvider;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('users can create a site for a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.sites.index', ['server' => $server])
        ->set('form.hostname', 'laravel.example.com')
        ->set('form.php_version', '8.4')
        ->set('form.type', 'Laravel')
        ->set('form.web_folder', '/public')
        ->set('form.zero_downtime', true)
        ->set('form.repository_url', 'git@github.com:laravel/laravel.git')
        ->set('form.repository_branch', 'main')
        ->set('form.use_deploy_key', true)
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->sites)->toHaveCount(1);

    $site = $server->sites()->first();
    expect($site->hostname)->toBe('laravel.example.com');
    expect($site->php_version)->toBe('8.4');
    expect($site->type)->toBe('Laravel');
    expect($site->web_folder)->toBe('/public');
    expect($site->zero_downtime)->toBeTrue();
    expect($site->repository_url)->toBe('git@github.com:laravel/laravel.git');
    expect($site->repository_branch)->toBe('main');
    expect($site->use_deploy_key)->toBeTrue();
});
