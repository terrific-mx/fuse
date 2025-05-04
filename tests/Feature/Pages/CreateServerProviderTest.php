<?php

use App\Models\ServerProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('successfully creates a server provider with valid credentials', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.server-providers.create')
        ->set('name', 'Test Provider')
        ->set('type', 'FakeServerProvider')
        ->set('token', 'valid-token')
        ->call('add');

    expect($user->serverProviders)->toHaveCount(1);

    expect($user->serverProviders()->first())
        ->name->toBe('Test Provider')
        ->type->toBe('FakeServerProvider')
        ->token->toBe('valid-token');
});

it('validates required field', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.server-providers.create')
        ->set('name', '')
        ->set('type', '')
        ->set('token', '')
        ->call('add')
        ->assertHasErrors(['name', 'type', 'token']);

    expect($user->serverProviders)->toHaveCount(0);
});

it('validates name uniqueness per user', function () {
    $user = User::factory()->create();

    ServerProvider::factory()->for($user)->create(['name' => 'Existing Provider']);

    Volt::actingAs($user)->test('pages.settings.server-providers.create')
        ->set('name', 'Existing Provider')
        ->call('add')
        ->assertHasErrors(['name']);

    expect($user->serverProviders)->toHaveCount(1);
});

it('validates token on server provider and delete provider if fails', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.server-providers.create')
        ->set('name', 'Test Provider')
        ->set('type', 'FakeServerProvider')
        ->set('token', 'invalid-token')
        ->call('add');

    expect($user->serverProviders)->toHaveCount(0);
});

it('shows the create server provider page to users', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/settings/server-providers/create')
        ->assertStatus(200);
});

it('redirects guests to login page', function () {
    get('/settings/server-providers/create')->assertRedirect('/login');
});
