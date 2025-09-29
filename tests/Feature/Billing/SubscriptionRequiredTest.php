<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('redirects to dashboard when switching to a subscribed organization', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $subscribedOrg = Organization::factory()->for($user)->withSubscription()->create();

    $unsubscribedOrg = Organization::factory()->for($user)->create();
    $user->switchOrganization($unsubscribedOrg);

    Volt::actingAs($user)->test('billing.subscription-required')
        ->call('switchOrganization', $subscribedOrg)
        ->assertRedirect(route('dashboard'));
});

it('stays on the page when switching to a non-subscribed organization', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $org1 = Organization::factory()->for($user)->create();
    $org2 = Organization::factory()->for($user)->create();

    $user->switchOrganization($org1);

    Volt::actingAs($user)->test('billing.subscription-required')
        ->call('switchOrganization', $org2)
        ->assertOk();
});

it('switches to an organization the user is a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $org = Organization::factory()->create();
    $org->addMember($user);

    $user->switchOrganization($user->organizations->first());

    Volt::actingAs($user)->test('billing.subscription-required')
        ->call('switchOrganization', $org)
        ->assertOk();
});

it('prevents switching to an organization the user neither owns nor is a member of', function () {
    /** @var User $user */
    $user = User::factory()->withPersonalOrganization()->create();

    $otherUser = User::factory()->withPersonalOrganization()->create();
    $otherOrg = $otherUser->organizations->first();

    $user->switchOrganization($user->organizations->first());

    Volt::actingAs($user)->test('billing.subscription-required')
        ->call('switchOrganization', $otherOrg)
        ->assertForbidden();
});
