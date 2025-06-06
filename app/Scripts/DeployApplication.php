<?php

namespace App\Scripts;

use App\Models\Application;
use App\Models\Deployment;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class DeployApplication extends Script
{
    public $sshAs = 'fuse';

    public function __construct(public Application $application, public Deployment $deployment) {}

    public function name()
    {
        return 'Deploying application';
    }

    public function timeout()
    {
        return 600;
    }

    public function script()
    {
        $latestFinishedDeployment = $this->application
            ->deployments()
            ->where('status', 'finished')
            ->latest()
            ->first();

        $environmentVariables = [];

        if ($this->application->type === 'laravel') {
            $environmentVariables['APP_KEY'] = 'base64:'.base64_encode(Encrypter::generateKey('AES-256-CBC'));
            $environmentVariables['APP_URL'] = $this->application->tls === 'off' ? "http://{$this->application->domain}" : "https://{$this->application->domain}";
        }

        if ($this->application->type === 'wordpress') {
            $vars = [
                'AUTH_KEY',
                'AUTH_SALT',
                'LOGGED_IN_KEY',
                'LOGGED_IN_SALT',
                'NONCE_KEY',
                'NONCE_SALT',
                'SECURE_AUTH_KEY',
                'SECURE_AUTH_SALT',
            ];

            foreach ($vars as $var) {
                $environmentVariables[$var] = str_replace(['&', '!', '$'], ['\&', '\!', '\$'], Str::random(64));
            }
        }

        return view('scripts.application.deploy-without-downtime', [
            'application' => $this->application,
            'repositoryDirectory' => "{$this->application->path()}/repository",
            'sharedDirectory' => "{$this->application->path()}/shared",
            'releasesDirectory' => "{$this->application->path()}/releases",
            'releaseDirectory' => "{$this->application->path()}/releases/{$this->deployment->created_at->timestamp}",
            'logsDirectory' => "{$this->application->path()}/logs",
            'latestFinishedDeployment' => $latestFinishedDeployment,
            'repositoryUrl' => "git@github.com:{$this->application->repository}.git",
            'zeroDowntimeDeployment' => true,
            'deployKeyPrivate' => null,
            'environmentVariables' => $environmentVariables,
            'currentDirectory' => "{$this->application->path()}/current",
        ])->render();
    }
}
