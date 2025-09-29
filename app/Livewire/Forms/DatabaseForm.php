<?php

namespace App\Livewire\Forms;

use App\Models\DatabaseUser;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DatabaseForm extends Form
{
    #[Validate('required|string|min:3|max:64')]
    public $name = '';

    #[Validate('boolean')]
    public $create_user = false;

    #[Validate('required_if:create_user,true|string|min:3|max:32')]
    public $user_name = '';

    #[Validate('required_if:create_user,true|string|min:8')]
    public $password = '';

    public function store($server): void
    {
        $this->validate();

        $database = $server->databases()->create([
            'name' => $this->name,
        ]);

        if ($this->create_user) {
            $user = DatabaseUser::create([
                'name' => $this->user_name,
                'password' => bcrypt($this->password),
            ]);

            $database->users()->attach($user->id);
        }

        $this->reset();
    }
}
