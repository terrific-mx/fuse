<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    public function createServer(): void
    {
        $user = auth()->user();
        $organization = $user->currentOrganization;

        $this->validate([
            'name' => [
                'required',
                'string',
                'max:253',
                'regex:/^(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)\.?([A-Za-z0-9-]{1,63}\.?)*[A-Za-z0-9]$/',
                'unique:servers,name,NULL,id,organization_id,' . $organization->id,
            ],
        ], [
            'name.regex' => __('The server name must be a valid hostname.'),
        ]);

        $organization->servers()->create([
            'name' => $this->name,
        ]);

        $this->name = '';
    }
}; ?>

<div>
    //
</div>
