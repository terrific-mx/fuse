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

        $server->run(new UpdateCaddyImports($server));
        $server->run(new DeleteFolder($path));

        $this->site->delete();

        return $this->redirect("/servers/{$server->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.sites.delete')
        <section>
            <form wire:submit="delete">
                <flux:button type="submit">Delete</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
