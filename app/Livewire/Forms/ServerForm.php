<?php

namespace App\Livewire\Forms;

use App\Jobs\ProvisionServer;
use App\Models\Organization;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServerForm extends Form
{
    public Organization $organization;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $ip_address = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'ipv4'],
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
            'ip_address' => $this->ip_address,
            'database_password' => Str::random(40),
            'sudo_password' => Str::random(40),
        ]);

        ProvisionServer::dispatch($server);

        $this->reset('name', 'ip_address');
    }
}
