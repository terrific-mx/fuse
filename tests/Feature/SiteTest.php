<?php

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('creates a site for a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.sites.index', ['server' => $server])
        ->set('form.hostname', 'laravel.example.com')
        ->set('form.php_version', '8.4')
        ->set('form.type', 'Laravel')
        ->set('form.web_folder', '/public')
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
    expect($site->repository_url)->toBe('git@github.com:laravel/laravel.git');
    expect($site->repository_branch)->toBe('main');
    expect($site->use_deploy_key)->toBeTrue();

    // New assertions for auto-filled attributes
    expect($site->shared_directory)->toBe('storage');
    expect($site->shared_files)->toBe(['.env']);
    expect($site->writeable_directories)->toBe(['storage', 'bootstrap/cache']);
    expect($site->script_before_deploy)->toBe('');
    expect($site->script_after_deploy)->toBe('');
    expect($site->script_before_activate)->toBe('');
    expect($site->script_after_activate)->toBe('');
});
