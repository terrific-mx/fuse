<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    public function markAsFinished()
    {
        $this->update(['status' => 'finished']);
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', 'pending');
    }
}
