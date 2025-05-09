@props(['application'])

<section class="space-y-8">
    <flux:heading size="xl" level="1">{{ __('Application') }} {{ $application->domain }}</flux:heading>
    <flux:subheading>
        {{ __('Server') }}:
        <flux:link href="/servers/{{ $application->server->id }}" wire:navigate>{{ $application->server->name }}</flux:link>
    </flux:subheading>

    <flux:separator />

    <div class="flex items-start max-md:flex-col">
        <div class="me-10 w-full pb-4 md:w-[220px] md:-my-1">
            <flux:navlist>
                <flux:navlist.item href="/applications/{{ $application->id }}/deployments" wire:navigate>{{ __('Deployments') }}</flux:navlist.item>
                <flux:navlist.item href="/applications/{{ $application->id }}/settings" wire:navigate>{{ __('Settings') }}</flux:navlist.item>
                <flux:navlist.item href="/applications/{{ $application->id }}/deployment-settings" wire:navigate>{{ __('Deployment Settings') }}</flux:navlist.item>
                <flux:navlist.item href="/applications/{{ $application->id }}/delete" wire:navigate>{{ __('Delete') }}</flux:navlist.item>
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
