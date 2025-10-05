<?php

namespace App\Livewire\Forms;

use App\Models\Site;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeploymentSettingsForm extends Form
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
        $this->shared_directories = $this->arrayToString($site->shared_directories ?? []);
        $this->shared_files = $this->arrayToString($site->shared_files ?? []);
        $this->writable_directories = $this->arrayToString($site->writable_directories ?? []);
        $this->script_before_deploy = $site->script_before_deploy ?? '';
        $this->script_after_deploy = $site->script_after_deploy ?? '';
        $this->script_before_activate = $site->script_before_activate ?? '';
        $this->script_after_activate = $site->script_after_activate ?? '';
    }

    public function update(): void
    {
        $this->validate();

        $this->site->update([
            'shared_directories' => $this->stringToArray($this->shared_directories),
            'shared_files' => $this->stringToArray($this->shared_files),
            'writable_directories' => $this->stringToArray($this->writable_directories),
            'script_before_deploy' => $this->script_before_deploy,
            'script_after_deploy' => $this->script_after_deploy,
            'script_before_activate' => $this->script_before_activate,
            'script_after_activate' => $this->script_after_activate,
        ]);
    }

    private function arrayToString(array $arr): string
    {
        return implode("\n", array_map('trim', $arr));
    }

    private function stringToArray(string $str): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $str))));
    }
}
