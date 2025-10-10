<?php

use App\Models\Deployment;
use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public Site $site;
    public Deployment $deployment;
}; ?>

<x-slot:breadcrumbs>
    @include('partials.site-breadcrumbs', ['server' => $server, 'site' => $site, 'current' => __('Deployments')])
</x-slot:breadcrumbs>

<div>
    @include('partials.site-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.site-navbar', ['server' => $server, 'site' => $site])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section>
                <header class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-sm:w-full sm:flex-1">
<flux:heading>{{ __('Deployment Output') }}</flux:heading>
<flux:text class="mt-2 max-w-prose">{{ __('This is the output log for your deployment. Review the log below for details about the deployment process.') }}</flux:text>
                    </div>
                </header>

                <div class="mt-4">
                    <pre>{{ $deployment->output }}</pre>
                </div>
            </section>
        </div>
    </div>
</div>
