<?php

use App\Models\User;
use Livewire\Volt\Volt;

it('adds an SSH key to the user organization', function () {
    $user = User::factory()->withPersonalOrganization(['name' => 'Test Org'])->create();
    $organization = $user->organizations()->first();

    $component = Volt::actingAs($user)
        ->test('ssh-keys')
        ->set('form.name', 'Work Laptop')
        ->set('form.public_key', 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey1')
        ->call('save');

    $component->assertHasNoErrors();

    $organization->refresh();
    expect($organization->sshKeys)->toHaveCount(1);

    $sshKey = $organization->sshKeys()->first();
    expect($sshKey->name)->toBe('Work Laptop');
    expect($sshKey->public_key)->toBe('ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey1');
});
