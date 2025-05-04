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

    expect((string) $script)->toContain('Do not remove this tls-* snippet');
});

it('can render the script for a site with tls custom', function () {
    //
})->todo();

it('can render the script for a site with tls internal', function () {
    $site = Site::factory()->create(['tls' => 'internal']);
    $script = new InstallCaddyfile($site);

    expect((string) $script)->toContain('tls internal');
});

it('can render the script for a static site', function () {
    //
})->todo();

it('can render the script for a wordpress site', function () {
    //
})->todo();

it('can render the script for a laravel site', function () {
    //
})->todo();
