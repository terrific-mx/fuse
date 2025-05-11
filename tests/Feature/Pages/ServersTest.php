<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('allows authenticated users to access the servers page', function () {
    /** @var User */
    $user = User::factory()->create();

    actingAs($user)->get('/servers')->assertOk();
});

it('redirects unauthenticated users to the login page when trying to access the applications page', function () {
    get('/servers')->assertRedirect('/login');
});

it('fetches only the servers associated with the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $serverA = Server::factory()->for($user)->create();
    $serverB = Server::factory()->for($otherUser)->create();
    $serverC = Server::factory()->for($user)->create();

    $component = Volt::actingAs($user)->test('pages.servers');

    expect($component->servers)->toHaveCount(2);
    expect($component->servers->pluck('id'))
        ->toContain($serverA->id)
        ->toContain($serverC->id)
        ->not->toContain($serverB->id);
});
