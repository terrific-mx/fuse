<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:253',
                'regex:/^(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)\.?([A-Za-z0-9-]{1,63}\.?)*[A-Za-z0-9]$/',
                Rule::unique('servers', 'name')->where(fn ($q) => $q->where('organization_id', $this->organization->id)),
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
        $this->validate();

        $this->organization->servers()->create([
            'name' => $this->name,
        ]);

        $this->name = '';
    }
}; ?>

<div>
    //
</div>
