<?php

use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Site $site;

    public function delete()
    {
        $this->site->delete();
    }
}; ?>

<x-layouts.app>
    @volt('pages.sites.delete')
        <section>
            <flux:button wire:click="delete">Delete</flux:button>
        </section>
    @endvolt
</x-layouts.app>
