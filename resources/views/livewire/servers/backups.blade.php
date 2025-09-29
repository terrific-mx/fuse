<?php

use App\Models\Server;
use Livewire\Volt\Component;

use App\Livewire\Forms\BackupForm;
use Livewire\Attributes\Computed;

new class extends Component {
    public Server $server;

    public BackupForm $form;

    public function save()
    {
        $this->form->store($this->server);
    }

    #[Computed]
    public function backups()
    {
        return $this->server->backups;
    }
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ custom: false, notifyFailure: false, notifySuccess: false }">
        <header>
            <flux:heading size="lg">{{ __('Add backup') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new backup for your server.') }}</flux:text>
        </header>

        <form wire:submit="save" class="space-y-6">
            <flux:input :label="__('Backup name')" wire:model="form.name" />

            <flux:select :label="__('Disk')" wire:model="form.disk">
                <flux:select.option value="local">Local</flux:select.option>
                <flux:select.option value="s3">Amazon S3</flux:select.option>
                <flux:select.option value="gcs">Google Cloud Storage</flux:select.option>
            </flux:select>

            <flux:pillbox multiple searchable :label="__('Databases to backup')" placeholder="Choose databases..." wire:model="form.databases">
                <flux:pillbox.option value="app_db">app_db</flux:pillbox.option>
                <flux:pillbox.option value="analytics">analytics</flux:pillbox.option>
                <flux:pillbox.option value="wordpress">wordpress</flux:pillbox.option>
                <flux:pillbox.option value="shop">shop</flux:pillbox.option>
            </flux:pillbox>

            <flux:textarea :label="__('Directories and file paths to backup')" :placeholder="__('One path per line')" wire:model="form.directories" />

            <flux:input :label="__('Number of backups to retain')" type="number" min="1" wire:model="form.retention" />

            <flux:switch label="{{ __('Custom backup frequency') }}" x-model="custom" />

            <div x-show="!custom">
                <flux:select :label="__('Frequency')" wire:model="form.frequency">
                    <flux:select.option value="* * * * *">{{ __('Every minute') }}</flux:select.option>
                    <flux:select.option value="*/5 * * * *">{{ __('Every 5 minutes') }}</flux:select.option>
                    <flux:select.option value="0 * * * *">{{ __('Hourly') }}</flux:select.option>
                    <flux:select.option value="0 0 * * *">{{ __('Daily') }}</flux:select.option>
                    <flux:select.option value="0 0 * * 0">{{ __('Weekly') }}</flux:select.option>
                    <flux:select.option value="0 0 1 * *">{{ __('Monthly') }}</flux:select.option>
                    <flux:select.option value="@reboot">{{ __('On reboot') }}</flux:select.option>
                </flux:select>
            </div>

            <div x-show="custom">
                <flux:input :label="__('Custom cron expression')" placeholder="* * * * *" wire:model="form.frequency" />
            </div>

            <div class="flex gap-6">
                <flux:switch label="{{ __('Notify on backup failure') }}" wire:model="form.notify_failure" />

                <flux:switch label="{{ __('Notify on backup success') }}" wire:model="form.notify_success" />
            </div>

            <div x-show="form.notify_failure || form.notify_success">
                <flux:input :label="__('Notification email')" type="email" wire:model="form.notification_email" />
            </div>

            <flux:button variant="primary" type="submit">{{ __('Add Backup') }}</flux:button>
        </form>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Backups') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of backups on this server.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Disk') }}</flux:table.column>
                <flux:table.column>{{ __('Databases') }}</flux:table.column>
                <flux:table.column>{{ __('Directories/Files') }}</flux:table.column>
                <flux:table.column>{{ __('Retention') }}</flux:table.column>
                <flux:table.column>{{ __('Frequency') }}</flux:table.column>
                <flux:table.column>{{ __('Notifications') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->backups as $backup)
                    <flux:table.row>
                        <flux:table.cell>{{ $backup->name }}</flux:table.cell>
                        <flux:table.cell>{{ $backup->disk }}</flux:table.cell>
                        <flux:table.cell>{{ implode(', ', $backup->databases ?? []) }}</flux:table.cell>
                        <flux:table.cell>{{ implode("\n", $backup->directories ?? []) }}</flux:table.cell>
                        <flux:table.cell>{{ $backup->retention }}</flux:table.cell>
                        <flux:table.cell>{{ $backup->frequency }}</flux:table.cell>
                        <flux:table.cell>
                            Failure: {{ $backup->notify_failure ? 'yes' : 'no' }},
                            Success: {{ $backup->notify_success ? 'yes' : 'no' }},
                            Email: {{ $backup->notification_email ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
