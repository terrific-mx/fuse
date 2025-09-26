<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="space-y-12">
    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Add site') }}</flux:heading>

        <flux:input :label="__('Hostname')" />

        <flux:select :label="__('PHP version')" placeholder="Choose PHP version...">
            <flux:select.option>8.4</flux:select.option>
            <flux:select.option>8.3</flux:select.option>
            <flux:select.option>8.1</flux:select.option>
        </flux:select>

        <flux:select :label="__('Site type')" placeholder="Choose site type...">
            <flux:select.option>Generic</flux:select.option>
            <flux:select.option>Laravel</flux:select.option>
            <flux:select.option>Static</flux:select.option>
            <flux:select.option>Wordpress</flux:select.option>
        </flux:select>

        <flux:input :label="__('Web folder')" value="/public" />

        <flux:checkbox label="Enable Zero Downtime Deployment" checked />

        <flux:fieldset>
            <flux:legend>Repository</flux:legend>

            <div class="space-y-6">
                <flux:input :label="__('Repository URL')" value="git@github.com:laravel/laravel.git" />

                <flux:input :label="__('Repository Branch')" value="main" />
            </div>
        </flux:fieldset>

        <flux:switch label="Use a deploy key" />

        <flux:button variant="primary">{{ __('Add Site') }}</flux:button>
    </section>
</div>
