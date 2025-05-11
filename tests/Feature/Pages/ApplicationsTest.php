<?php

use App\Models\Application;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('allows authenticated users to access the applications page', function () {
    /** @var User */
    $user = User::factory()->create();

    actingAs($user)->get('/applications')->assertOk();
});

it('redirects unauthenticated users to the login page when trying to access the applications page', function () {
    get('/applications')->assertRedirect('/login');
});

it('fetches only the applications associated with the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $applicationA = Application::factory()->for(Server::factory()->for($user))->create();
    $applicationB = Application::factory()->for(Server::factory()->for($otherUser))->create();
    $applicationC = Application::factory()->for(Server::factory()->for($user))->create();

    $component = Volt::actingAs($user)->test('pages.applications');

    expect($component->applications)->toHaveCount(2);
    expect($component->applications->pluck('id'))
        ->toContain($applicationA->id)
        ->toContain($applicationC->id)
        ->not->toContain($applicationB->id);
});
