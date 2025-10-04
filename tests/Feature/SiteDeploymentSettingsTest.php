<?php

use App\Models\Server;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('allows a user to update deployment settings for a site', function () {
    $site = Site::factory()->create([
        'shared_directories' => ['storage'],
        'shared_files' => ['.env'],
        'writable_directories' => [
            'bootstrap/cache',
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ],
        'script_before_deploy' => '',
        'script_after_deploy' => '',
        'script_before_activate' => 'composer install',
        'script_after_activate' => '',
    ]);

    $component = Volt::actingAs($site->server->organization->user)
        ->test('servers.sites.deployment-settings', [
            'server' => $site->server,
            'site' => $site,
        ])
        ->set('form.shared_directories', "storage\npublic/uploads")
        ->set('form.shared_files', ".env\nconfig/app.php")
        ->set('form.writable_directories', "storage\npublic/uploads")
        ->set('form.script_before_deploy', 'echo before deploy')
        ->set('form.script_after_deploy', 'echo after deploy')
        ->set('form.script_before_activate', 'php artisan migrate')
        ->set('form.script_after_activate', 'php artisan cache:clear')
        ->call('save');

    $component->assertHasNoErrors();

    $site->refresh();
    expect($site->shared_directories)->toBe(['storage', 'public/uploads']);
    expect($site->shared_files)->toBe(['.env', 'config/app.php']);
    expect($site->writable_directories)->toBe(['storage', 'public/uploads']);
    expect($site->script_before_deploy)->toBe('echo before deploy');
    expect($site->script_after_deploy)->toBe('echo after deploy');
    expect($site->script_before_activate)->toBe('php artisan migrate');
    expect($site->script_after_activate)->toBe('php artisan cache:clear');
});
