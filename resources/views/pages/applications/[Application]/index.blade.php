<?php

use App\Models\Application;

use function Laravel\Folio\render;

render(function (Application $application) {
    return redirect("/applications/{$application->id}/deployments");
});
