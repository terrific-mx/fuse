<?php

namespace App\Policies;

use App\Models\FirewallRule;
use App\Models\User;

class FirewallRulePolicy
{
    /**
     * Determine whether the user can view the firewall rule.
     */
    public function view(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentOrganization->id === $firewallRule->server->organization_id;
    }

    /**
     * Determine whether the user can update the firewall rule.
     */
    public function update(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentOrganization->id === $firewallRule->server->organization_id;
    }

    /**
     * Determine whether the user can delete the firewall rule.
     */
    public function delete(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentOrganization->id === $firewallRule->server->organization_id;
    }
}
