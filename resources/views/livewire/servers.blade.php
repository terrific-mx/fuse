<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    protected function rules(): array
    {
        $organizationId = auth()->user()->currentOrganization->id;

        return [
            'name' => [
                'required',
                'string',
                'max:253',
                'regex:/^(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)\.?([A-Za-z0-9-]{1,63}\.?)*[A-Za-z0-9]$/',
                \Illuminate\Validation\Rule::unique('servers', 'name')->where(fn ($q) => $q->where('organization_id', $organizationId)),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.regex' => __('The server name must be a valid hostname.'),
        ];
    }

    public function createServer(): void
    {
        $user = auth()->user();
        $organization = $user->currentOrganization;

        $this->validate();

        $organization->servers()->create([
            'name' => $this->name,
        ]);

        $this->name = '';
    }
}; ?>

<div>
    //
</div>
