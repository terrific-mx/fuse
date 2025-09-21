<?php

namespace App\Policies;

use App\Models\ServerCredential;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServerCredentialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServerCredential $serverCredential): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServerCredential $serverCredential): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServerCredential $serverCredential): bool
    {
        // User must belong to the credential's organization
        return $user->organizations()->where('id', $serverCredential->organization_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServerCredential $serverCredential): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServerCredential $serverCredential): bool
    {
        return false;
    }
}
