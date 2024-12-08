<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only users with the 'admin' role can view this resource
        return $user->hasRole('Administrator');
    }
    /**
     * Determine if the given user can delete the specified user record.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent users from deleting their own account
        return $user->id !== $model->id;
    }
    /**
     * Determine if the given user can bulk delete user records.
     */
    public function deleteAny(User $user): bool
    {
        // Allow bulk delete action in general
        return true;
    }
}
