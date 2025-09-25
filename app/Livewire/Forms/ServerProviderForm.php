<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\ServerProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServerProviderForm extends Form
{
    #[Validate]
    public $name = '';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('server_providers')->where(fn ($query) => $query->where('organization_id', Auth::user()->currentOrganization->id)),
            ],
            'type' => 'required|in:Hetzner Cloud',
            'meta' => 'required|array',
        ];
    }

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
