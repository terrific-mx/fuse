<div class="me-10 w-full pb-4 md:w-[220px]">
    <flux:navlist>
        <flux:navlist.item
            :href="route('servers.show', $server)"
            wire:navigate
        >
            {{ __('Overview') }}
        </flux:navlist.item>
        <flux:navlist.item
            :href="route('servers.sites.index', $server)"
            wire:navigate
        >
            {{ __('Sites') }}
        </flux:navlist.item>
    </flux:navlist>
</div>
