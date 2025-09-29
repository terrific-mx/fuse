<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DaemonForm extends Form
{
    #[Validate('required|string|max:255')]
    public $command = '';

    #[Validate('nullable|string|max:255')]
    public $directory = '';

    #[Validate('required|string|max:255')]
    public $user = '';

    #[Validate('required|integer|min:1')]
    public $processes = 1;

    #[Validate('required|integer|min:0')]
    public $stop_wait = 10;

    #[Validate('required|string|max:255')]
    public $stop_signal = 'TERM';

    public function store($server): void
    {
        $this->validate();

        $server->daemons()->create([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->user,
            'processes' => $this->processes,
            'stop_wait' => $this->stop_wait,
            'stop_signal' => $this->stop_signal,
        ]);

        $this->reset();
    }
}
