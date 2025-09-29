<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class BackupForm extends Form
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|in:local,s3,gcs')]
    public $disk = 'local';

    #[Validate('required|array')]
    public $databases = [];

    #[Validate('required|string')]
    public $directories = '';

    #[Validate('required|integer|min:1')]
    public $retention = 15;

    #[Validate('required|string|max:255')]
    public $frequency = '0 0 * * *';

    #[Validate('boolean')]
    public $notify_failure = false;

    #[Validate('boolean')]
    public $notify_success = false;

    #[Validate('nullable|email|max:255')]
    public $notification_email = '';

    public function store($server): void
    {
        $this->validate();

        $this->directories = collect(explode("\n", $this->directories))
            ->map(fn($path) => trim($path))
            ->filter()
            ->values()
            ->toArray();

        $backup = $server->backups()->create([
            'name' => $this->name,
            'disk' => $this->disk,
            'databases' => $this->databases,
            'directories' => $this->directories,
            'retention' => $this->retention,
            'frequency' => $this->frequency,
            'notify_failure' => $this->notify_failure,
            'notify_success' => $this->notify_success,
            'notification_email' => $this->notification_email,
        ]);

        $this->reset();
    }
}
