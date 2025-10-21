<?php

namespace App\Policies;

use App\Models\Cronjob;
use App\Models\User;

class CronjobPolicy
{
    /**
     * Determine whether the user can delete the cronjob.
     */
    public function delete(User $user, Cronjob $cronjob): bool
    {
        return $user->currentOrganization->id === $cronjob->server->organization_id;
    }
}
