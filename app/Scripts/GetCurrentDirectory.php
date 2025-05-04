<?php

namespace App\Scripts;

class GetCurrentDirectory extends Script
{
    public $name = 'Echoing Current Directory';

    public function script()
    {
        return 'pwd';
    }

    public function timeout()
    {
        return 10;
    }
}
