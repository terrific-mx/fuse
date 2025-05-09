@props(['server'])

<section class="space-y-8">
    <div>
        <flux:heading size="xl" level="1">{{ __('Server') }} {{ $server->name }}</flux:heading>
        <flux:text class="mt-2">{{ $server->public_address }}</flux:text>
    </div>

    <flux:separator />

    <div class="flex items-start max-md:flex-col">
        <div class="me-10 w-full pb-4 md:w-[220px] md:-my-1">
            <flux:navlist>
                <flux:navlist.item href="/servers/{{ $server->id }}/applications" wire:navigate>{{ __('Applications') }}</flux:navlist.item>
            </flux:navlist>
        </div>

        <flux:separator class="md:hidden" />

        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</section>
