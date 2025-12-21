<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\Audit;
use App\Features\SystemManagements\Models\User;

/**
 * Class AuditPolicy
 * @package App\Features\SystemManagements\Policies
 */
class AuditPolicy
{
    /**
     * Determine whether the user can view any audits.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('audit.viewAny');
    }

    /**
     * Determine whether the user can view the audit.
     */
    public function view(User $user, Audit $audit): bool
    {
        return $user->hasPermission('audit.view');
    }

    /**
     * Determine whether the user can create audits.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('audit.create');
    }

    /**
     * Determine whether the user can update the audit.
     */
    public function update(User $user, Audit $audit): bool
    {
        // Audits are generally immutable, but allow for admin corrections
        return $user->hasPermission('audit.update');
    }

    /**
     * Determine whether the user can delete the audit.
     */
    public function delete(User $user, Audit $audit): bool
    {
        // Only allow deletion for cleanup purposes
        return $user->hasPermission('audit.delete');
    }

    /**
     * Determine whether the user can export audits.
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('audit.export');
    }

    /**
     * Determine whether the user can cleanup old audits.
     */
    public function cleanup(User $user): bool
    {
        return $user->hasPermission('audit.cleanup');
    }

    /**
     * Determine whether the user can view security alerts.
     */
    public function securityAlerts(User $user): bool
    {
        return $user->hasPermission('audit.securityAlerts');
    }
}
