<?php

use App\Models\Application;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

use function Laravel\Folio\middleware;
use function Laravel\Folio\render;

middleware(['auth', 'can:view,application', ValidateSessionWithWorkOS::class]);

render(function (Application $application) {
    return redirect("/applications/{$application->id}/deployments");
});
