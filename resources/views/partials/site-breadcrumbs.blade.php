<flux:breadcrumbs>
    <flux:breadcrumbs.item href="{{ route('servers.show', $server) }}" wire:navigate>{{ $server->name }}</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="{{ route('servers.sites.show', [$server, $site]) }}" wire:navigate>{{ $site->hostname }}</flux:breadcrumbs.item>
    @if (!empty($current))
        <flux:breadcrumbs.item>{{ $current }}</flux:breadcrumbs.item>
    @endif
</flux:breadcrumbs>
