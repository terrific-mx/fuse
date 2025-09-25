<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\ServerProvider;

class ServerProviderForm extends Form
{
    public $name = '';


    #[Validate('required|in:Hetzner Cloud')]
    public $type = '';

    #[Validate('required|array')]
    public $meta = [];

    public function store($organization)
    {
        \Validator::make([
            'name' => $this->name,
            'type' => $this->type,
            'meta' => $this->meta,
        ], [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:server_providers,name,NULL,id,organization_id,' . \Auth::user()->currentOrganization->id,
            ],
            'type' => 'required|in:Hetzner Cloud',
            'meta' => 'required|array',
        ])->validate();

        ServerProvider::create([
            'organization_id' => $organization->id,
            'name' => $this->name,
            'type' => $this->type,
            'meta' => $this->meta,
        ]);

        $this->reset();
    }
}
