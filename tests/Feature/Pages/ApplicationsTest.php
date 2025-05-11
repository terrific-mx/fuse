<?php

use App\Models\Application;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('authenticated users can see the applications page', function () {
    /** @var User */
    $user = User::factory()->create();

    actingAs($user)->get('/applications')->assertOk();
});

it('only auhtenticated users can see the applications page', function () {
    get('/applications')->assertRedirect('/login');
});

it('only fetch applications for the authenticated user', function () {
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
