<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($site->hostname)" color="auto" initials:single :color:seed="$site->id" />
            <flux:heading class="text-xl">{{ $site->hostname }}</flux:heading>
            <flux:text class="ml-4 text-zinc-500 text-sm">
                <flux:icon.server variant="micro" class="inline-block mr-1 fill-zinc-400 dark:fill-zinc-500" />
                <a :href="route('servers.show', $server)" class="hover:underline" wire:navigate>{{ $server->name }}</a>
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

    <flux:text class="mt-4">
        {{ $site->repository_url }}<span class="mx-2">|</span>{{ $site->repository_branch }}<span class="mx-2">|</span>PHP {{ $site->php_version }}
    </flux:text>

    <flux:spacer class="mt-8" />

    <div class="border-b border-zinc-200 dark:border-zinc-700 overflow-hidden overflow-x-scroll">
        <flux:navbar class="-mb-px">
            <flux:navbar.item :href="route('servers.sites.show', [$server, $site])" :accent="false" wire:navigate>Home</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.deployments', [$server, $site])" :accent="false" wire:navigate>Deployments</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:spacer class="mt-10" />

    {{ $slot }}
</div>
