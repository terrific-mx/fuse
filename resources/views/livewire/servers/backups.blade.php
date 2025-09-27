<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ custom: false, notifyFailure: false, notifySuccess: false }">
        <header>
            <flux:heading size="lg">{{ __('Add backup') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new backup for your server.') }}</flux:text>
        </header>

        <flux:input :label="__('Backup name')" />

        <flux:select :label="__('Disk')">
            <flux:select.option value="local">Local</flux:select.option>
            <flux:select.option value="s3">Amazon S3</flux:select.option>
            <flux:select.option value="gcs">Google Cloud Storage</flux:select.option>
        </flux:select>

        <flux:pillbox multiple searchable :label="__('Databases to backup')" placeholder="Choose databases...">
            <flux:pillbox.option value="app_db">app_db</flux:pillbox.option>
            <flux:pillbox.option value="analytics">analytics</flux:pillbox.option>
            <flux:pillbox.option value="wordpress">wordpress</flux:pillbox.option>
            <flux:pillbox.option value="shop">shop</flux:pillbox.option>
        </flux:pillbox>

        <flux:textarea :label="__('Directories and file paths to backup')" :placeholder="__('One path per line')" />

        <flux:input :label="__('Number of backups to retain')" type="number" min="1" value="15" />

        <flux:switch label="{{ __('Custom backup frequency') }}" x-model="custom" />
        <div x-show="!custom">
            <flux:select :label="__('Frequency')">
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
            <flux:input :label="__('Custom cron expression')" placeholder="* * * * *" />
        </div>

        <div class="flex gap-6">
            <flux:switch label="{{ __('Notify on backup failure') }}" x-model="notifyFailure" />
            <flux:switch label="{{ __('Notify on backup success') }}" x-model="notifySuccess" />
        </div>
        <div x-show="notifyFailure || notifySuccess">
            <flux:input :label="__('Notification email')" type="email" />
        </div>

        <flux:button variant="primary">{{ __('Add Backup') }}</flux:button>
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
                <flux:table.row>
                    <flux:table.cell>Daily DB & Files</flux:table.cell>
                    <flux:table.cell>local</flux:table.cell>
                    <flux:table.cell>mysql</flux:table.cell>
                    <flux:table.cell>/var/www/html
/storage/uploads</flux:table.cell>
                    <flux:table.cell>7</flux:table.cell>
                    <flux:table.cell>0 0 * * *</flux:table.cell>
                    <flux:table.cell>Failure: yes, Success: no, Email: admin@example.com</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Weekly S3 Backup</flux:table.cell>
                    <flux:table.cell>s3</flux:table.cell>
                    <flux:table.cell>postgres</flux:table.cell>
                    <flux:table.cell>/srv/data</flux:table.cell>
                    <flux:table.cell>4</flux:table.cell>
                    <flux:table.cell>0 0 * * 0</flux:table.cell>
                    <flux:table.cell>Failure: yes, Success: yes, Email: backups@example.com</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Hourly Redis</flux:table.cell>
                    <flux:table.cell>local</flux:table.cell>
                    <flux:table.cell>redis</flux:table.cell>
                    <flux:table.cell>/var/cache</flux:table.cell>
                    <flux:table.cell>24</flux:table.cell>
                    <flux:table.cell>0 * * * *</flux:table.cell>
                    <flux:table.cell>Failure: no, Success: yes, Email: redis@example.com</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
