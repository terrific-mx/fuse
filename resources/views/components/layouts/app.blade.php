<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        <div class="mx-auto max-w-6xl">
            {{ $slot }}
        </div>
    </flux:main>
</x-layouts.app.sidebar>
