<?php

use App\Jobs\DeleteSite;
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

        DeleteSite::dispatch($server, $this->site->path());

        $this->site->delete();

        return $this->redirect("/servers/{$server->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.sites.delete')
    <section class="space-y-6">
            <div>
                <flux:link href="/servers/{{ $site->server->id }}" class="text-sm">
                    {{ __('Back') }}
                </flux:link>
            </div>

            <flux:heading>{{ __('Deleting Site') }}: {{ $site->domain }}</flux:heading>

            <form wire:submit="delete">
                <flux:button type="submit">Delete</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
