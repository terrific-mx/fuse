<?php

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;

    #[Validate]
    public string $repository = '';

    #[Validate]
    public string $branch = '';

    #[Validate('required|max:255')]
    public string $web_directory = '';

    #[Validate('required|in:PHP 8.3,PHP 8.2,PHP 8.1')]
    public string $php_version = 'PHP 8.3';

    protected function rules()
    {
        return [
            'repository' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->application->sourceProvider->client()->validRepository($value)) {
                        $fail(__('The selected repository is invalid for the chosen source provider.'));
                    }
                }
            ],
            'branch' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->application->sourceProvider->client()->validBranch($value, $this->repository)) {
                        $fail(__('The selected repository is invalid for the chosen source provider.'));
                    }
                }
            ],
        ];
    }

    public function mount() {
        $this->repository = $this->application->repository;
        $this->branch = $this->application->branch;
        $this->web_directory = $this->application->web_directory;
        $this->php_version = $this->application->php_version;
    }

    public function save()
    {
        $this->validate();

        $this->application->update([
            'repository' => $this->repository,
            'branch' => $this->branch,
            'web_directory' => $this->web_directory,
            'php_version' => $this->php_version,
        ]);
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.settings')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Application settings') }}</flux:heading>

                <flux:separator />

                <flux:input wire:model="repository" name="repository" label="{{ __('Repository') }}" placeholder="terrific-mx/fuse" required />

                <flux:input wire:model="branch" name="branch" label="{{ __('Branch') }}" placeholder="main" required />

                <flux:input wire:model="web_directory" name="web_directory" label="{{ __('Web Directory') }}" placeholder="/public" required />

                <flux:select wire:model="php_version" name="php_version" label="{{ __('PHP Version') }}" required>
                    <flux:select.option value=""></flux:select.option>
                    <flux:select.option value="PHP 8.3">PHP 8.3</flux:select.option>
                    <flux:select.option value="PHP 8.2">PHP 8.2</flux:select.option>
                    <flux:select.option value="PHP 8.1">PHP 8.1</flux:select.option>
                </flux:select>

                <flux:separator variant="subtle" />

                <div class="flex justify-end gap-4">
                    <flux:button variant="primary" type="submit">{{ __('Save settings') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
