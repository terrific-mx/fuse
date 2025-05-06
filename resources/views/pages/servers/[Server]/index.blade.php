<?php

use App\Models\Server;

use function Laravel\Folio\render;

render(function (Server $server) {
    return redirect("/servers/{$server->id}/applications");
});
