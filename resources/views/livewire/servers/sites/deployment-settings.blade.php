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
            <flux:heading size="lg">{{ __('Deployment Settings') }}</flux:heading>

            <flux:input :label="__('Notification Email')" :placeholder="__('Enter email for deployment notifications')" :value="'deploy@example.com'" type="email" required />

            <flux:input :label="__('Releases to Retain')" :placeholder="__('Number of releases to keep')" :value="3" type="number" min="1" required />

            <flux:fieldset>
                <flux:legend>{{ __('Shared Directories') }}</flux:legend>
                <flux:textarea :label="__('One directory per line')" :placeholder="__('e.g. storage, public/uploads')" rows="4">
storage
public/uploads
logs</flux:textarea>
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>{{ __('Shared Files') }}</flux:legend>
                <flux:textarea :label="__('One file per line')" :placeholder="__('e.g. .env, config/app.php')" rows="4">
.env
config/app.php
.env.production</flux:textarea>
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>{{ __('Writeable Directories') }}</flux:legend>
                <flux:textarea :label="__('One directory per line')" :placeholder="__('e.g. storage, bootstrap/cache')" rows="4">
storage
bootstrap/cache
tmp</flux:textarea>
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>{{ __('Deployment Scripts') }}</flux:legend>
                <div class="space-y-4">
                    <flux:textarea :label="__('Before Updating Repository')" :placeholder="__('Enter bash script to run before updating repository')" rows="3">
echo Pre-update script running
export VAR=1</flux:textarea>
                    <flux:textarea :label="__('After Updating Repository')" :placeholder="__('Enter bash script to run after updating repository')" rows="3">
echo After update
php artisan migrate
</flux:textarea>
                    <flux:textarea :label="__('Before Activating New Release')" :placeholder="__('Enter bash script to run before activating new release')" rows="3">
echo Before activate
chmod -R 755 storage</flux:textarea>
                    <flux:textarea :label="__('After Activating New Release')" :placeholder="__('Enter bash script to run after activating new release')" rows="3">
echo After activate
service php8.4-fpm restart</flux:textarea>
                </div>
            </flux:fieldset>

            <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
        </form>
    </section>
</div>
