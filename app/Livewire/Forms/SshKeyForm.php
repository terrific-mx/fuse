<?php

namespace App\Livewire\Forms;

use App\Models\SshKey;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Organization;

class SshKeyForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string')]
    public string $public_key = '';

    public function store(Organization $organization): void
    {
        $this->validate();

        $organization->sshKeys()->create([
            'name' => $this->name,
            'public_key' => $this->public_key,
        ]);

        $this->reset();
    }
}
