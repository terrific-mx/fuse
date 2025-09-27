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
        <!-- Add site settings details here -->
    </section>
</div>
