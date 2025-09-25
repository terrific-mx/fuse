<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\ServerProvider;

class ServerProviderForm extends Form
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|in:Hetzner Cloud')]
    public $type = '';

    #[Validate('required|array')]
    public $meta = [];

    public function store($organization)
    {
        $this->validate();

        ServerProvider::create([
            'organization_id' => $organization->id,
            'name' => $this->name,
            'type' => $this->type,
            'meta' => $this->meta,
        ]);

        $this->reset();
    }
}
