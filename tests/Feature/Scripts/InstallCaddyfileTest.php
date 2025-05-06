<?php

use App\Models\Application;
use App\Scripts\InstallCaddyfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the script to be run', function () {
    $application = Application::factory()->create(['domain' => 'example.com']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('example.com:443');
});

it('can render the script for an application domain that starts with www', function () {
    $application = Application::factory()->create(['domain' => 'www.example.com']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('www.example.com:443');
});

it('can render the script for an application domain that does not starts with www', function () {
    $application = Application::factory()->create(['domain' => 'example.com']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('example.com:443');
});

it('can render the script for an application with tls auto', function () {
    $application = Application::factory()->create(['tls' => 'auto']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->not->toContain('tls internal');
});

it('can render the script for an application with tls custom', function () {
    $application = Application::factory()->create(['tls' => 'custom']);
    $script = new InstallCaddyfile($application);
})->todo();

it('can render the script for an application with tls internal', function () {
    $application = Application::factory()->create(['tls' => 'internal']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('tls internal');
});

it('can render the script for a static application', function () {
    $application = Application::factory()->create(['type' => 'static']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->not->toContain('php_fastcgi');
});

it('can render the script for a wordpress application', function () {
    $application = Application::factory()->create(['type' => 'wordpress']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('path /wp-content/uploads/*.php');
});

it('can render the script for a laravel application', function () {
    $application = Application::factory()->create(['type' => 'laravel']);
    $script = new InstallCaddyfile($application);

    expect((string) $script)->toContain('php_fastcgi');
});
