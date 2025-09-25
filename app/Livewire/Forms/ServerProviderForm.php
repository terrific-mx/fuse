<?php

namespace App\Livewire\Forms;

use App\Models\ServerProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ServerProviderForm extends Form
{
    #[Validate]
    public string $name = '';

    #[Validate('required|in:Hetzner Cloud')]
    public string $type = '';

    #[Validate('required|array')]
    public array $meta = [];

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

    public function store($organization)
    {
        $this->validate();

        $provider = ServerProvider::create([
            'organization_id' => $organization->id,
            'name' => $this->name,
            'type' => $this->type,
            'meta' => $this->meta,
        ]);

        if (! $provider->client()->isTokenValid()) {
            $provider->delete();

            $this->addError('meta.token', __('The credentials provided were not valid.'));

            return;
        }

        $this->reset();
    }
}
