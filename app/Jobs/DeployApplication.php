<?php

namespace App\Jobs;

use App\Callbacks\CheckDeployment;
use App\Models\Application;
use App\Models\Deployment;
use App\Models\Server;
use App\Scripts\DeployApplicationWithoutDowntime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeployApplication implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public Application $application, public Deployment $deployment)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->runInBackground(new DeployApplicationWithoutDowntime(
            $this->application,
            $this->deployment,
        ), [
            'then' => [new CheckDeployment($this->deployment->id)],
        ]);
    }
}
