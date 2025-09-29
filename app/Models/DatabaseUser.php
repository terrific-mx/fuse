<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseUser extends Model
{
    /** @use HasFactory<\Database\Factories\DatabaseUserFactory> */
    use HasFactory;

    protected $guarded = [];

    public function databases()
    {
        return $this->belongsToMany(Database::class, 'database_database_user');
    }
}
