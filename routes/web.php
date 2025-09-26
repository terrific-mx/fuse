<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\OrganizationInvitationAcceptController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Volt::route('server-providers', 'server-providers.index')->name('server-providers.index');
    Volt::route('source-providers', 'source-providers.index')->name('source-providers.index');

    Volt::route('servers', 'servers.index')->name('servers.index');
    Volt::route('servers/{server}', 'servers.show')->name('servers.show');

    Volt::route('servers/{server}/sites', 'servers.sites.index')->name('servers.sites.index');
    Volt::route('servers/{server}/databases', 'servers.databases.index')->name('servers.databases.index');
    Volt::route('servers/{server}/cronjobs', 'servers.cronjobs.index')->name('servers.cronjobs.index');
    Volt::route('servers/{server}/daemons', 'servers.daemons.index')->name('servers.daemons.index');
    Volt::route('servers/{server}/firewall-rules', 'servers.firewall-rules')->name('servers.firewall-rules');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('organizations/{organization}/settings/members', 'organizations.settings.members')
        ->name('organizations.settings.members');
    Volt::route('organizations/{organization}/settings/general', 'organizations.settings.general')
        ->name('organizations.settings.general');
    Volt::route('organizations/{organization}', 'organizations.settings.general')
        ->name('organizations.show');

    Route::get('organizations/invitations/{invitation}/accept', OrganizationInvitationAcceptController::class)
        ->middleware('signed')
        ->name('organizations.invitations.accept');
});

require __DIR__.'/auth.php';

require __DIR__.'/billing.php';
