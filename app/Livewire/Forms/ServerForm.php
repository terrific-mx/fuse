<?php

namespace App\Livewire\Forms;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServerForm extends Form
{
    public Organization $organization;

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

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function store(): void
    {
        $this->validate();

        $server = $this->organization->servers()->create([
            'name' => $this->name,
            'provider_id' => $this->provider_id,
            'region' => $this->region,
            'type' => $this->type,
        ]);

        $server->update([
            'provider_server_id' => 'simulated-' . $server->id,
        ]);

        $this->reset('name', 'provider_id', 'region', 'type');
    }
}
