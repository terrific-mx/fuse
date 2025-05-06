<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    /** @use HasFactory<\Database\Factories\DeploymentFactory> */
    use HasFactory;

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
