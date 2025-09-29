<?php

namespace App\Livewire\Forms;

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
}
