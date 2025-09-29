<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class FirewallRuleForm extends Form
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|in:allow,deny,reject')]
    public $action = 'allow';

    #[Validate('required|integer|min:1|max:65535')]
    public $port = 22;

    #[Validate('nullable|string|max:255')]
    public $from_ip = '';

    public function store($server): void
    {
        $this->validate();

        $server->firewallRules()->create([
            'name' => $this->name,
            'action' => $this->action,
            'port' => $this->port,
            'from_ip' => $this->from_ip,
        ]);

        $this->reset();
    }
}
