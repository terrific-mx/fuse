<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($server->name)" color="auto" initials:single :color:seed="'server-'.$server->id" />
            <flux:heading class="text-xl">{{ $server->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.group heading="Security">
                    <flux:menu.item :href="route('servers.passwords', $server)" wire:navigate icon="key" icon:variant="micro">
                        Passwords
                    </flux:menu.item>
                </flux:menu.group>
                <flux:menu.group heading="Configuration">
                    <flux:menu.item disabled>Caddy</flux:menu.item>
                    <flux:menu.item disabled>MySQL</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.1 ini</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.1 FPM</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.2 ini</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.2 FPM</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.3 ini</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.3 FPM</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.4 ini</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.4 FPM</flux:menu.item>
                    <flux:menu.item disabled>Composer auth</flux:menu.item>
                </flux:menu.group>
                <flux:menu.group heading="Services">
                    <flux:menu.item disabled>Caddy</flux:menu.item>
                    <flux:menu.item disabled>MySQL</flux:menu.item>
                    <flux:menu.item disabled>Redis</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.1</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.2</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.3</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.4</flux:menu.item>
                </flux:menu.group>
                <flux:menu.group heading="Logs">
                    <flux:menu.item disabled>Caddy access log</flux:menu.item>
                    <flux:menu.item disabled>Caddy error log</flux:menu.item>
                    <flux:menu.item disabled>MySQL error log</flux:menu.item>
                    <flux:menu.item disabled>Redis log</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.1 FPM log</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.2 FPM log</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.3 FPM log</flux:menu.item>
                    <flux:menu.item disabled>PHP 8.4 FPM log</flux:menu.item>
                </flux:menu.group>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:text class="mt-4">
        {{ $server->ip_address }}
    </flux:text>

    <flux:spacer class="mt-8" />

    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar class="-mb-px overflow-hidden overflow-x-scroll">
            <flux:navbar.item :href="route('servers.show', $server)" :accent="false" wire:navigate>Home</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.index', $server)" :accent="false" wire:navigate>Sites</flux:navbar.item>
            <flux:dropdown>
                <flux:navbar.item icon:trailing="chevron-down">Resources</flux:navbar.item>
                <flux:navmenu>
                    <flux:navmenu.item :href="route('servers.databases.index', $server)" :accent="false" wire:navigate icon="circle-stack" icon:variant="micro">Databases</flux:navmenu.item>
                    <flux:navmenu.item :href="route('servers.cronjobs.index', $server)" :accent="false" wire:navigate icon="clock" icon:variant="micro">Cronjobs</flux:navmenu.item>
                    <flux:navmenu.item :href="route('servers.daemons.index', $server)" :accent="false" wire:navigate icon="cpu-chip" icon:variant="micro">Daemons</flux:navmenu.item>
                    <flux:navmenu.item :href="route('servers.firewall-rules.index', $server)" :accent="false" wire:navigate icon="shield-check" icon:variant="micro">Firewall rules</flux:navmenu.item>
                </flux:navmenu>
            </flux:dropdown>
            <flux:navbar.item disabled>Backups</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:spacer class="mt-10" />

    {{ $slot }}
</div>
