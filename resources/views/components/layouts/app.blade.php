<x-layouts.app.header :title="$title ?? null">
    <flux:main container>
        {{ $slot }}
    </flux:main>
    <livewire:organizations.create />
    <flux:toast />
</x-layouts.app.header>
