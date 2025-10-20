<?php

namespace App\Http\Controllers;

use App\Models\Server;

class SetupRootSshController extends Controller
{
    public function show(Server $server)
    {
        return response(
            view('scripts.server.setup-root-ssh', ['server' => $server])->render()
        )->header('Content-Type', 'text/x-shellscript');
    }
}
