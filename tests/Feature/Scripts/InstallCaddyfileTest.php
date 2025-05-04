<?php

use App\Models\Site;
use App\Scripts\InstallCaddyfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the script to be run', function () {
    $site = Site::factory()->create(['domain' => 'example.com']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('example.com:443');
});

it('can render the script for a site domain that starts with www', function () {
    $site = Site::factory()->create(['domain' => 'www.example.com']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('www.example.com:443');
});

it('can render the script for a site domain that does not starts with www', function () {
    $site = Site::factory()->create(['domain' => 'example.com']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('example.com:443');
});

it('can render the script for a site with tls auto', function () {
    $site = Site::factory()->create(['tls' => 'auto']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->not->toContain('tls internal');
});

it('can render the script for a site with tls custom', function () {
    $site = Site::factory()->create(['tls' => 'custom']);
    $script = new InstallCaddyfile($site);
})->todo();

it('can render the script for a site with tls internal', function () {
    $site = Site::factory()->create(['tls' => 'internal']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('tls internal');
});

it('can render the script for a static site', function () {
    $site = Site::factory()->create(['type' => 'static']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->not->toContain('php_fastcgi');
});

it('can render the script for a wordpress site', function () {
    $site = Site::factory()->create(['type' => 'wordpress']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('path /wp-content/uploads/*.php');
});

it('can render the script for a laravel site', function () {
    $site = Site::factory()->create(['type' => 'laravel']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('php_fastcgi');
});
