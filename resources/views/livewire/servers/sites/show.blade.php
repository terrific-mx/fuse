<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;

    public function mount()
    {
        $this->authorize('view', $this->site);
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.site-breadcrumbs', ['server' => $server, 'site' => $site, 'current' => __('Overview')])
</x-slot:breadcrumbs>

<div>
    @include('partials.site-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.site-navbar', ['server' => $server, 'site' => $site])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section class="space-y-6 max-w-lg">
                <header>
                    <flux:heading>{{ __('Site overview') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('View details and configuration for this site.') }}</flux:text>
                </header>
                <flux:input :label="__('Hostname')" value="{{ $site->hostname }}" readonly variant="filled" />
                <flux:input :label="__('PHP version')" value="{{ $site->php_version }}" readonly variant="filled" />
                <flux:input :label="__('Repository URL')" value="{{ $site->repository_url }}" readonly variant="filled" />
                <flux:input :label="__('Repository Branch')" value="{{ $site->repository_branch }}" readonly variant="filled" />
            </section>
        </div>
    </div>
</div>
