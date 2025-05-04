<?php

use App\Models\User;
use Laravel\WorkOS\User as WorkOSUser;
use App\SecureShellKey;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;

Route::get('login', function (AuthKitLoginRequest $request) {
    return $request->redirect();
})->middleware(['guest'])->name('login');

Route::get('authenticate', function (AuthKitAuthenticationRequest $request) {
    return tap(to_route('dashboard'), fn () => $request->authenticate(
        createUsing: function (WorkOSUser $user) {
            return User::create([
                'name' => $user->firstName . ' ' . $user->lastName,
                'email' => $user->email,
                'email_verified_at' => now(),
                'workos_id' => $user->id,
                'avatar' => $user->avatar ?? '',
                'keypair' => SecureShellKey::forNewUser(),
            ]);
        },
    ));
})->middleware(['guest']);

Route::post('logout', function (AuthKitLogoutRequest $request) {
    return $request->logout();
})->middleware(['auth'])->name('logout');
