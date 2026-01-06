<?php

namespace App\Policies;

use App\Models\CulturalActivity;
use App\Models\User;

class CulturalActivityPolicy
{
    /**
     * Determine if the user can view the activity
     */
    public function view(User $user, CulturalActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }

    /**
     * Determine if the user can update the activity
     */
    public function update(User $user, CulturalActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }

    /**
     * Determine if the user can delete the activity
     */
    public function delete(User $user, CulturalActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }
}
