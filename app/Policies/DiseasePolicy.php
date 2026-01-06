<?php

namespace App\Policies;

use App\Models\Disease;
use App\Models\User;

class DiseasePolicy
{
    /**
     * Determine if the user can view the disease
     */
    public function view(User $user, Disease $disease): bool
    {
        return $user->id === $disease->user_id;
    }

    /**
     * Determine if the user can update the disease
     */
    public function update(User $user, Disease $disease): bool
    {
        return $user->id === $disease->user_id;
    }

    /**
     * Determine if the user can delete the disease
     */
    public function delete(User $user, Disease $disease): bool
    {
        return $user->id === $disease->user_id;
    }
}
