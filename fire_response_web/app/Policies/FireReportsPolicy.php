<?php

namespace App\Policies;

use App\Models\FireReports;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FireReportsPolicy
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
    public function view(User $user, FireReports $fireReports): bool
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
    public function update(User $user, FireReports $fireReports): Response
    {
        return $user->id === $fireReports->user_id
            ? Response::allow()
            : Response::deny('You cannot edit this report.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FireReports $fireReports): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FireReports $fireReports): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FireReports $fireReports): bool
    {
        return false;
    }
}
