<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServerForm extends Form
{
    #[Validate]
    public string $name = '';

    #[Validate('required|exists:server_providers,id')]
    public ?int $provider_id = null;

    #[Validate]
    public string $region = '';

    #[Validate]
    public string $type = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:server_providers,id'],
            'region' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ];
    }

    public function store()
    {
        $this->validate();

        $server = Auth::user()->currentOrganization->servers()->create([
            'name' => $this->name,
            'provider_id' => $this->provider_id,
            'region' => $this->region,
            'type' => $this->type,
        ]);

        $this->reset();

        return $server;
    }
}
