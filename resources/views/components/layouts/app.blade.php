<x-layouts.app.sidebar :title="$title ?? null" :breadcrumbs="$breadcrumbs ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    <livewire:organizations.create />
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
</x-layouts.app.sidebar>
