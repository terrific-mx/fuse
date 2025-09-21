<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $provider = '';
    public string $name = '';
    public array $credentials = [];

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    protected function rules(): array
    {
        return [
            'provider' => [
                'required',
                'string',
                Rule::unique('server_credentials')->where(fn ($query) => $query->where('organization_id', $this->organization->id)),
            ],
            'name' => ['required', 'string'],
            'credentials' => ['required', 'array'],
        ];
    }

    public function addCredential()
    {
        $validated = $this->validate();

        $this->organization->serverCredentials()->create($validated);
    }
}; ?>

<div>
    //
</div>
