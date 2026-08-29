<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\Restaurant;
use Illuminate\Auth\Access\Response;

class FeedbackPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Restaurant $restaurant): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Restaurant $restaurant, Feedback $feedback): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Restaurant $restaurant): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Restaurant $restaurant, Feedback $feedback): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Restaurant $restaurant, Feedback $feedback): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Restaurant $restaurant, Feedback $feedback): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Restaurant $restaurant, Feedback $feedback): bool
    {
        return false;
    }
}
