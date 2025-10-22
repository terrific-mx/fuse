<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Service extends Model
{
    use Sushi;

    protected $rows = [
        ['name' => 'Caddy 2', 'restart_command' => '/usr/sbin/service caddy reload'],
        ['name' => 'MySQL 8.0', 'restart_command' => 'service mysql restart'],
        ['name' => 'PHP 8.1', 'restart_command' => 'service php8.1-fpm restart'],
        ['name' => 'PHP 8.2', 'restart_command' => 'service php8.2-fpm restart'],
        ['name' => 'PHP 8.3', 'restart_command' => 'service php8.3-fpm restart'],
        ['name' => 'PHP 8.4', 'restart_command' => 'service php8.4-fpm restart'],
        ['name' => 'Redis 6', 'restart_command' => 'service redis-server restart'],
    ];
}
