<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\FAQ;
use App\Features\SystemManagements\Models\User;

/**
 * Class FAQPolicy
 * @package App\Features\SystemManagements\Policies
 */
class FAQPolicy
{
    /**
     * Determine whether the user can view any FAQs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('faq.viewAny');
    }

    /**
     * Determine whether the user can view the FAQ.
     */
    public function view(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('faq.view');
    }

    /**
     * Determine whether the user can create FAQs.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('faq.create');
    }

    /**
     * Determine whether the user can update the FAQ.
     */
    public function update(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('faq.update');
    }

    /**
     * Determine whether the user can delete the FAQ.
     */
    public function delete(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('faq.delete');
    }

    /**
     * Determine whether the user can restore the FAQ.
     */
    public function restore(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('faq.restore');
    }

    /**
     * Determine whether the user can permanently delete the FAQ.
     */
    public function forceDelete(User $user, FAQ $faq): bool
    {
        return $user->hasPermission('faq.forceDelete');
    }

}
