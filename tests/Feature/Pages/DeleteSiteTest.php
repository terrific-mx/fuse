<?php

use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can delete a site', function () {
    $site = Site::factory()->create();
    $user = $site->server->user;

    Volt::actingAs($user)->test('pages.sites.delete', ['site' => $site])
        ->call('delete');

    expect(Site::count())->toBe(0);
});

it('run script to delete site folder', function () {

})->todo();

it('runs script to upade all caddy imports', function () {

})->todo();

it('checks only authorized users can delete sites', function () {

})->todo();
