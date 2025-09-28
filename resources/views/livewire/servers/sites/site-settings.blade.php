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
        <form class="space-y-6">
            <flux:heading size="lg">{{ __('Site Settings') }}</flux:heading>

            <flux:select :label="__('PHP version')" :value="$site->php_version" placeholder="Choose PHP version..." required>
                <flux:select.option value="8.4">8.4</flux:select.option>
                <flux:select.option value="8.3">8.3</flux:select.option>
                <flux:select.option value="8.1">8.1</flux:select.option>
            </flux:select>

            <flux:input :label="__('Web folder')" :value="$site->web_folder" required />

            <flux:fieldset>
                <flux:legend>{{ __('Repository') }}</flux:legend>
                <div class="space-y-6">
                    <flux:input :label="__('Repository URL')" :value="$site->repository_url" />
                    <flux:input :label="__('Repository Branch')" :value="$site->repository_branch" />
                </div>
            </flux:fieldset>

            <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
        </form>
    </section>
</div>
