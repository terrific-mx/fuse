<?php

use App\Http\Controllers\CallbackController;
use App\Http\Controllers\OrganizationInvitationAcceptController;
use App\Http\Controllers\SetupRootSshController;
use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('pricing', 'pricing')->name('pricing');

Route::get('/task/{task}/callback', [CallbackController::class, 'task'])->name('task.callback');

Route::get('servers/{server}/setup-root-ssh', [SetupRootSshController::class, 'show'])
    ->middleware('signed')
    ->name('servers.setup-root-ssh');

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::redirect('/dashboard', '/servers')->name('dashboard');

    Volt::route('ssh-keys', 'ssh-keys.index')->name('ssh-keys.index');
    Volt::route('ssh-keys/create', 'ssh-keys.create')->name('ssh-keys.create');
    Volt::route('ssh-keys/{sshKey}/edit', 'ssh-keys.edit')->name('ssh-keys.edit');

    Volt::route('servers', 'servers.index')->name('servers.index');
    Volt::route('servers/create', 'servers.create')->name('servers.create');
    Route::redirect('servers/{server}', '/servers/{server}/sites')->name('servers.show');

    Volt::route('servers/{server}/sites', 'servers.sites.index')->name('servers.sites.index');
    Volt::route('servers/{server}/sites/{site}', 'servers.sites.show')->name('servers.sites.show');
    Volt::route('servers/{server}/sites/{site}/deployments', 'servers.sites.deployments')->name('servers.sites.deployments');
    Volt::route('servers/{server}/sites/{site}/deployment-settings', 'servers.sites.deployment-settings')->name('servers.sites.deployment-settings');
    Volt::route('servers/{server}/sites/{site}/files', 'servers.sites.files')->name('servers.sites.files');

    Volt::route('servers/{server}/daemons', 'servers.daemons.index')->name('servers.daemons.index');
    Volt::route('servers/{server}/daemons/create', 'servers.daemons.create')->name('servers.daemons.create');

    Volt::route('servers/{server}/databases', 'servers.databases.index')->name('servers.databases.index');
    Volt::route('servers/{server}/databases/create', 'servers.databases.create')->name('servers.databases.create');
    Volt::route('servers/{server}/provision-instructions', 'servers.provision-instructions')->name('servers.provision-instructions');

    Volt::route('servers/{server}/cronjobs', 'servers.cronjobs.index')->name('servers.cronjobs.index');
    Volt::route('servers/{server}/cronjobs/create', 'servers.cronjobs.create')->name('servers.cronjobs.create');
    Volt::route('servers/{server}/cronjobs/{cronjob}/edit', 'servers.cronjobs.edit')->name('servers.cronjobs.edit');

    Volt::route('servers/{server}/firewall-rules', 'servers.firewall-rules.index')->name('servers.firewall-rules.index');
    Volt::route('servers/{server}/firewall-rules/create', 'servers.firewall-rules.create')->name('servers.firewall-rules.create');
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
