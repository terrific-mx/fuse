<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Service extends Model
{
    use Sushi;

    protected $rows = [
        ['name' => 'Caddy 2'],
        ['name' => 'MySQL 8.0'],
        ['name' => 'PHP 8.1'],
        ['name' => 'PHP 8.2'],
        ['name' => 'PHP 8.3'],
        ['name' => 'PHP 8.4'],
        ['name' => 'Redis 6'],
    ];
}
