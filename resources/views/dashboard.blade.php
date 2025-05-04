<x-layouts.app :title="__('Dashboard')">
    <section class="space-y-5">
        <flux:navlist class="w-64">
            <flux:navlist.item href="/settings/server-providers">
                {{ __('Server Providers') }}
            </flux:navlist.item>

            <flux:navlist.item href="/settings/source-control">
                {{ __('Source Control') }}
            </flux:navlist.item>

            <flux:navlist.item href="/servers">
                {{ __('Servers') }}
            </flux:navlist.item>
        </flux:navlist>
    </section>
</x-layouts.app>
