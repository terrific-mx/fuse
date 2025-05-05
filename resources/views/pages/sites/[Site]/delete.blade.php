<?php

use App\Models\Site;
use App\Scripts\DeleteFolder;
use App\Scripts\UpdateCaddyImports;
use Livewire\Volt\Component;

new class extends Component {
    public Site $site;

    public function delete()
    {
        $this->authorize($this->site, 'delete');

        $server = $this->site->server;
        $path = $this->site->path();
        $this->site->delete();

        $server->run(new UpdateCaddyImports($server));
        $server->run(new DeleteFolder($path));
    }
}; ?>

<x-layouts.app>
    @volt('pages.sites.delete')
        <section>
            <flux:button wire:click="delete">Delete</flux:button>
        </section>
    @endvolt
</x-layouts.app>
