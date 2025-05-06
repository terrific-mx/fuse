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
        $path = $this->application->path();

        $caddyfile = view('scripts.application._caddyfile', [
            'application' => $this->application,
            'domainStartsWithWww' => Str::of($this->application->domain)->startsWith('www.'),
            'tlsSetting' => $this->application->tls,
            'address' => $this->application->domain,
            'port' => 443,
            'path' => $path,
            'webDirectory' => "{$path}/repository/public",
            'phpSocket' => '/run/php/php8.3-fpm.sock',
        ])->render();

        return view('scripts.application.install-caddyfile', [
            'application' => $this->application,
            'caddyfile' => $caddyfile,
            'caddyfilePath' => "{$path}/Caddyfile",
            'tmpCaddyFilePath' => "{$path}/Caddyfile.".Str::random(),
            'webDirectory' => "{$path}/repository/public",
        ])->render();
    }
}
