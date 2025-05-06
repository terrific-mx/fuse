<?php

use App\Jobs\DeleteSite;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can delete a site', function () {
    Process::fake();
    $site = Site::factory()->create();
    $user = $site->user();

    Volt::actingAs($user)->test('pages.sites.delete', ['site' => $site])
        ->call('delete');

    expect(Site::count())->toBe(0);
});

it('runs script to upade all caddy imports', function () {
    Process::fake();
    $site = Site::factory()->create();
    $user = $site->user();

    Volt::actingAs($user)->test('pages.sites.delete', ['site' => $site])
        ->call('delete');

    expect($site->server->tasks->first())->name->toBe('Updating Caddy Imports');
});

it('run script to delete site folder', function () {
    Process::fake();
    $site = Site::factory()->create();
    $user = $site->user();

    Volt::actingAs($user)->test('pages.sites.delete', ['site' => $site])
        ->call('delete');

    expect($site->server->tasks)->toHaveCount(2);
    expect($site->server->tasks->last())->name->toBe('Deleting folder');
});

it('checks only authorized users can delete sites', function () {
    Process::fake();
    $site = Site::factory()->create();
    $user = $site->user();
    $anotherUser = User::factory()->create();

    Volt::actingAs($anotherUser)->test('pages.sites.delete', ['site' => $site])
        ->call('delete')
        ->assertForbidden();
});

it('dispatches a job to delete the site from the server', function () {
    Queue::fake();
    $site = Site::factory()->create();
    $user = $site->user();

    Volt::actingAs($user)->test('pages.sites.delete', ['site' => $site])
        ->call('delete');

    Queue::assertPushed(DeleteSite::class);
});
