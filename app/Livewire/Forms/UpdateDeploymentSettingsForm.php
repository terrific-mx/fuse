<?php

namespace App\Livewire\Forms;

use App\Models\Site;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateDeploymentSettingsForm extends Form
{
    public ?Site $site = null;

    #[Validate('array')]
    public array $shared_directories = [];

    #[Validate('array')]
    public array $shared_files = [];

    #[Validate('array')]
    public array $writable_directories = [];

    #[Validate('string')]
    public string $script_before_deploy = '';

    #[Validate('string')]
    public string $script_after_deploy = '';

    #[Validate('string')]
    public string $script_before_activate = '';

    #[Validate('string')]
    public string $script_after_activate = '';

    public function setSite(Site $site): void
    {
        $this->site = $site;
        $this->shared_directories = $site->shared_directories ?? [];
        $this->shared_files = $site->shared_files ?? [];
        $this->writable_directories = $site->writable_directories ?? [];
        $this->script_before_deploy = $site->script_before_deploy ?? '';
        $this->script_after_deploy = $site->script_after_deploy ?? '';
        $this->script_before_activate = $site->script_before_activate ?? '';
        $this->script_after_activate = $site->script_after_activate ?? '';
    }

    public function update(): void
    {
        $this->validate();

        $this->site->update([
            'shared_directories' => $this->shared_directories,
            'shared_files' => $this->shared_files,
            'writable_directories' => $this->writable_directories,
            'script_before_deploy' => $this->script_before_deploy,
            'script_after_deploy' => $this->script_after_deploy,
            'script_before_activate' => $this->script_before_activate,
            'script_after_activate' => $this->script_after_activate,
        ]);
    }
}
