<?php

namespace App\Callbacks;

use App\Models\Site;
use App\Models\Task;

class MarkCaddyInstalled
{
    public function __construct(public int $site_id) {}

    /**
     * Mark the site as having its Caddy file installed.
     */
    public function __invoke(Task $task)
    {
        $site = Site::findOrFail($this->site_id);

        $site->update(['caddy_installed_at' => now()]);
    }

    /**
     * Get the array representation of this callback for storing in the task.
     *
     * @return array{class: string, args: array<string, mixed>}
     */
    public function toCallbackArray(): array
    {
        return [
            'class' => self::class,
            'args' => [
                'site_id' => $this->site_id,
            ],
        ];
    }
}
