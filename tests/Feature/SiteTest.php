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
        ->set('form.repository_url', 'git@github.com:laravel/laravel.git')
        ->set('form.repository_branch', 'main')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->sites)->toHaveCount(1);

    $site = $server->sites()->first();
    expect($site->hostname)->toBe('laravel.example.com');
    expect($site->php_version)->toBe('8.4');
    expect($site->repository_url)->toBe('git@github.com:laravel/laravel.git');
    expect($site->repository_branch)->toBe('main');

    // New assertions for auto-filled attributes
    expect($site->shared_directories)->toBe(['storage']);
    expect($site->shared_files)->toBe(['.env']);
    expect($site->writeable_directories)->toBe([
        'bootstrap/cache',
        'storage',
        'storage/app',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
    ]);
    expect($site->script_before_deploy)->toBe('');
    expect($site->script_after_deploy)->toBe(<<<'EOT'
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
        npm install --prefer-offline --no-audit
        npm run build
        $PHP_BINARY artisan storage:link
        $PHP_BINARY artisan config:cache
        $PHP_BINARY artisan route:cache
        $PHP_BINARY artisan view:cache
        $PHP_BINARY artisan event:cache
        # $PHP_BINARY artisan migrate --force
        EOT);
    expect($site->script_before_activate)->toBe('');
    expect($site->script_after_activate)->toBe('');

    // Assert an initial deployment is created for the site
    expect($site->deployments)->toHaveCount(1);
    $deployment = $site->deployments()->first();
    expect($deployment->site->is($site))->toBeTrue();
    expect($deployment->status)->toBe('pending');
    expect($deployment->triggeredBy->is($server->organization->user))->toBeTrue();
});
