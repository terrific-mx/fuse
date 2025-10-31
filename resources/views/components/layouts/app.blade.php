<x-layouts.app.header :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    @auth
        <livewire:organizations.create />
    @endauth

    <flux:toast />
</x-layouts.app.header>
