<?php

use App\Models\Server;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

use function Laravel\Folio\middleware;
use function Laravel\Folio\render;

middleware(['auth', 'can:view,server', ValidateSessionWithWorkOS::class]);

render(function (Server $server) {
    return redirect("/servers/{$server->id}/applications");
});
