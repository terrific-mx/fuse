<?php

namespace App\Scripts;

use App\Models\Application;
use Illuminate\Support\Str;

class InstallCaddyfile extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Application $application) {}

    public function name()
    {
        return 'Installing Caddyfile';
    }

    public function timeout()
    {
        return 30;
    }

    public function script()
    {
        $caddyfile = view('scripts.application._caddyfile', [
            'application' => $this->application,
            'domainStartsWithWww' => Str::of($this->application->domain)->startsWith('www.'),
            'tlsSetting' => $this->application->tls,
            'address' => $this->application->domain,
            'port' => 443,
            'webDirectory' => "{$this->application->path()}/current/public",
            'phpSocket' => $this->application->php_socket_path,
        ])->render();

        return view('scripts.application.install-caddyfile', [
            'application' => $this->application,
            'caddyfile' => $caddyfile,
            'caddyfilePath' => "{$this->application->path()}/Caddyfile",
            'tmpCaddyFilePath' => "{$this->application->path()}/Caddyfile.".Str::random(),
            'webDirectory' => "{$this->application->path()}/current/public",
        ])->render();
    }
}
