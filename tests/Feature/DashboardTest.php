<?php

use App\Models\User;

it('redirects guests to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

it('allows authenticated users to visit the dashboard', function () {
    $user = User::factory()->withPersonalOrganizationAndSubscription()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});
