<?php

namespace App\Jobs;

use App\Callbacks\CheckDeployment;
use App\Models\Application;
use App\Models\Server;
use App\Scripts\DeployApplication;
use App\Scripts\InstallCaddyfile;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallApplication implements ShouldQueue
{
    use Queueable;

    public Server $server;

    /**
     * Create a new job instance.
     */
    public function __construct(public Application $application)
    {
        $this->server = $application->server;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->run(new InstallCaddyfile($this->application));

        $this->application->update(['status' => 'installed']);

        $this->server->run(new UpdateCaddyImports($this->server));

        $deployment = $this->application->deployments()->create(['status' => 'pending']);

        $this->server->runInBackground(new DeployApplication(
            $this->application,
            $deployment,
        ), [
            'then' => [new CheckDeployment($deployment->id)],
        ]);
    }
}
