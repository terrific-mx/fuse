<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('redirects guests to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

it('allows authenticated users to visit the dashboard', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();

    actingAs($user)->get('/dashboard')->assertRedirect();
});
