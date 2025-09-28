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

    <section class="space-y-6" x-data="{ ssl_mode: 'auto' }">
        <form class="space-y-6">
            <flux:heading size="lg">{{ __('SSL Settings') }}</flux:heading>

            <flux:radio.group x-model="ssl_mode" label="{{ __('SSL Mode') }}">
                <flux:radio value="auto" label="{{ __('Auto') }}" description="{{ __('The TLS certificate for your site is generated automatically using a trusted certificate authority (Let’s Encrypt).') }}" />
                <flux:radio value="caddy" label="{{ __('Caddy') }}" description="{{ __('The TLS certificate for your site is generated internally, rather than relying on an external certificate authority. Useful for development environments.') }}" />
                <flux:radio value="custom" label="{{ __('Custom') }}" description="{{ __('Provide your own TLS certificate and private key for your site.') }}" />
                <flux:radio value="off" label="{{ __('Off') }}" description="{{ __('TLS is completely disabled for this site. Not recommended for production.') }}" />
            </flux:radio.group>

            <div x-show="ssl_mode === 'custom'" class="space-y-6">
                <flux:input type="textarea" :label="__('TLS Certificate')" wire:model.defer="ssl_certificate" placeholder="{{ __('Paste your certificate here...') }}" required />
                <flux:input type="textarea" :label="__('Private Key')" wire:model.defer="ssl_private_key" placeholder="{{ __('Paste your private key here...') }}" required />
            </div>

            <flux:button type="submit" variant="primary">{{ __('Save SSL Settings') }}</flux:button>
        </form>
    </section>
</div>
