<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DatabaseUserForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('array')]
    public array $databases = [];

    public function store($server): void
    {
        $this->validate();

        $user = $server->databaseUsers()->create([
            'name' => $this->name,
            'password' => bcrypt($this->password),
            'server_id' => $server->id,
        ]);

        $user->databases()->sync($this->databases);

        $this->reset();
    }
}
