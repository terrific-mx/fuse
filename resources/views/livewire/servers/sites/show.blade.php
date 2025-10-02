<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;
}; ?>

<div class="space-y-12">
    @include('partials.site-navbar', ['server' => $server, 'site' => $site])

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Site Overview') }}</flux:heading>

        <flux:input :label="__('Hostname')" value="{{ $site->hostname }}" readonly variant="filled" />

        <flux:input :label="__('PHP version')" value="{{ $site->php_version }}" readonly variant="filled" />

        <flux:input :label="__('Repository URL')" value="{{ $site->repository_url }}" readonly variant="filled" />

        <flux:input :label="__('Repository Branch')" value="{{ $site->repository_branch }}" readonly variant="filled" />
    </section>
</div>
