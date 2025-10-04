<?php

namespace App\Livewire\Forms;

use App\Models\Site;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateDeploymentSettingsForm extends Form
{
    public ?Site $site = null;

    #[Validate('string')]
    public string $shared_directories = '';

    #[Validate('string')]
    public string $shared_files = '';

    #[Validate('string')]
    public string $writable_directories = '';

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
        $this->shared_directories = isset($site->shared_directories) ? implode("\n", $site->shared_directories) : '';
        $this->shared_files = isset($site->shared_files) ? implode("\n", $site->shared_files) : '';
        $this->writable_directories = isset($site->writable_directories) ? implode("\n", $site->writable_directories) : '';
        $this->script_before_deploy = $site->script_before_deploy ?? '';
        $this->script_after_deploy = $site->script_after_deploy ?? '';
        $this->script_before_activate = $site->script_before_activate ?? '';
        $this->script_after_activate = $site->script_after_activate ?? '';
    }

    public function update(): void
    {
        $this->validate();

        $this->site->update([
            'shared_directories' => $this->splitLines($this->shared_directories),
            'shared_files' => $this->splitLines($this->shared_files),
            'writable_directories' => $this->splitLines($this->writable_directories),
            'script_before_deploy' => $this->script_before_deploy,
            'script_after_deploy' => $this->script_after_deploy,
            'script_before_activate' => $this->script_before_activate,
            'script_after_activate' => $this->script_after_activate,
        ]);
    }

    private function splitLines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $value))));
    }

}
