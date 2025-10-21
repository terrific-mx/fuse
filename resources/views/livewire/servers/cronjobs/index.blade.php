<?php

use App\Models\Cronjob;
use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public function delete(Cronjob $cronjob)
    {
        $this->authorize('delete', $cronjob);
        $cronjob->uninstall();
    }
}; ?>

<div>
    <flux:button :href="route('servers.cronjobs.create', $server)" wire:navigate>Create</flux:button>
</div>
