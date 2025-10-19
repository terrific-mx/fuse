<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SetupRootSshController extends Controller
{
    use AuthorizesRequests;

    public function show(Server $server)
    {
        $this->authorize('view', $server);

        return response(
            view('scripts.server.setup-root-ssh', ['server' => $server])->render()
        )->header('Content-Type', 'text/x-shellscript');
    }
}
