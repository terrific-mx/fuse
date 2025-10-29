<x-layouts.app.header :title="$title ?? null">
    <flux:main container>
        {{ $slot }}
    </flux:main>

    @auth
        <livewire:organizations.create />
    @endauth

    <flux:toast />
</x-layouts.app.header>
