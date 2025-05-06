<?php

namespace App\Scripts;

use App\Models\Site;
use Illuminate\Support\Str;

class InstallCaddyfile extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Site $site)
    {
    }

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
        $path = $this->site->path();

        $caddyfile = view('scripts.site._caddyfile', [
            'site' => $this->site,
            'domainStartsWithWww' => Str::of($this->site->domain)->startsWith('www.'),
            'tlsSetting' => $this->site->tls,
            'address' => $this->site->domain,
            'port' => 443,
            'path' => $path,
            'webDirectory' => "{$path}/repository/public",
            'siteType' => $this->site->type,
            'phpSocket' => '/run/php/php8.3-fpm.sock',
        ])->render();

        return view('scripts.site.install-caddyfile', [
            'site' => $this->site,
            'caddyfile' => $caddyfile,
            'caddyfilePath' => "{$path}/Caddyfile",
            'tmpCaddyFilePath' => "{$path}/Caddyfile.".Str::random(),
            'webDirectory' => "{$path}/repository/public",
        ])->render();
    }
}
