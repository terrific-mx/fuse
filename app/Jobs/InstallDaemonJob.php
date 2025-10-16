<?php

namespace App\Jobs;

use App\Models\Daemon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InstallDaemonJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Daemon $daemon,
    ) {}

    public function handle(): void
    {
        $daemon = $this->daemon;
        $server = $daemon->server;

        // Render supervisor config
        $supervisorConfig = view('scripts.daemon.supervisor-conf', [
            'daemon' => $daemon,
        ])->render();

        $configPath = "/etc/supervisor/conf.d/daemon-{$daemon->id}.conf";

        // Shell script to write config and reload supervisor
        $script = <<<BASH
            cat <<'EOF' > {$configPath}
            {$supervisorConfig}
            EOF
            supervisorctl reread
            supervisorctl update
            BASH;

        $task = $server->tasks()->create([
            'name' => 'install_daemon',
            'user' => 'root',
            'script' => $script,
            'payload' => [],
            'after_actions' => [],
        ]);

        $task->run();
    }
}
