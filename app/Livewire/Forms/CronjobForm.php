<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CronjobForm extends Form
{
    #[Validate('required|string|max:255')]
    public $command = '';

    #[Validate('required|string|max:255')]
    public $user = '';

    #[Validate('required|string|max:255')]
    public $expression = '';

    public function store($server): void
    {
        $this->validate();

        $server->cronjobs()->create([
            'command' => $this->command,
            'user' => $this->user,
            'expression' => $this->expression,
        ]);

        $this->reset();
    }
}
