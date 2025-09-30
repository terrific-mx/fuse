<?php

use App\Http\Controllers\CallbackController;
use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\OrganizationInvitationAcceptController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/callback/task/{id}', [CallbackController::class, 'task']);

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Volt::route('server-providers', 'server-providers.index')->name('server-providers.index');
    Volt::route('source-providers', 'source-providers.index')->name('source-providers.index');
    Volt::route('ssh-keys', 'ssh-keys')->name('ssh-keys');

    Volt::route('servers', 'servers.index')->name('servers.index');
    Volt::route('servers/{server}', 'servers.show')->name('servers.show');

    Volt::route('servers/{server}/sites', 'servers.sites.index')->name('servers.sites.index');
    Volt::route('servers/{server}/sites/{site}', 'servers.sites.show')->name('servers.sites.show');
    Volt::route('servers/{server}/sites/{site}/deployments', 'servers.sites.deployments')->name('servers.sites.deployments');
    Volt::route('servers/{server}/sites/{site}/site-settings', 'servers.sites.site-settings')->name('servers.sites.site-settings');
    Volt::route('servers/{server}/sites/{site}/deployment-settings', 'servers.sites.deployment-settings')->name('servers.sites.deployment-settings');
    Volt::route('servers/{server}/sites/{site}/ssl', 'servers.sites.ssl')->name('servers.sites.ssl');
    Volt::route('servers/{server}/sites/{site}/files', 'servers.sites.files')->name('servers.sites.files');
    Volt::route('servers/{server}/sites/{site}/logs', 'servers.sites.logs')->name('servers.sites.logs');

    Volt::route('servers/{server}/databases', 'servers.databases')->name('servers.databases');
    Volt::route('servers/{server}/cronjobs', 'servers.cronjobs')->name('servers.cronjobs');
    Volt::route('servers/{server}/daemons', 'servers.daemons')->name('servers.daemons');
    Volt::route('servers/{server}/firewall-rules', 'servers.firewall-rules')->name('servers.firewall-rules');
    Volt::route('servers/{server}/backups', 'servers.backups')->name('servers.backups');
    Volt::route('servers/{server}/services', 'servers.services')->name('servers.services');
    Volt::route('servers/{server}/files', 'servers.files')->name('servers.files');
    Volt::route('servers/{server}/logs', 'servers.logs')->name('servers.logs');
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
