<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($site->hostname)" color="auto" initials:single :color:seed="'site-'. $site->id" />
            <flux:heading class="text-xl">{{ $site->hostname }}</flux:heading>
            <flux:text class="inline-flex gap-2 items-center" inline>
                <flux:icon.server variant="micro" />
                <flux:link :href="route('servers.show', $server)" :accent="false" wire:navigate>{{ $server->name }}</flux:link>
            </flux:text>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />
            <flux:menu>
                <flux:menu.item :href="route('servers.sites.deployment-settings', [$server, $site])" wire:navigate icon="cog" icon:variant="micro">
                    Deployment settings
                </flux:menu.item>
                <flux:menu.item :href="route('servers.sites.files', [$server, $site])" wire:navigate icon="document" icon:variant="micro">
                    Edit files
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:spacer class="mt-4" />

    <div class="flex gap-3">
        <flux:text>{{ $site->repository_url }}</flux:text>
        <flux:separator vertical class="my-1" />
        <flux:text>{{ $site->repository_branch }}</flux:text>
        <flux:separator vertical class="my-1" />
        <flux:text>PHP {{ $site->php_version }}</flux:text>
    </div>

    <flux:spacer class="mt-8" />

    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar class="-mb-px overflow-hidden overflow-x-scroll">
            <flux:navbar.item :href="route('servers.sites.show', [$server, $site])" :accent="false" wire:navigate>Home</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.deployments', [$server, $site])" :accent="false" wire:navigate>Deployments</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:spacer class="mt-10" />

    {{ $slot }}
</div>
