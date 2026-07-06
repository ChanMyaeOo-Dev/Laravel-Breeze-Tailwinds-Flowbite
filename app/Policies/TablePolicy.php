<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\Table;

class TablePolicy
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
    public function view(Restaurant $restaurant, Table $table): bool
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
    public function update(Restaurant $restaurant, Table $table): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Restaurant $restaurant, Table $table): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Restaurant $restaurant, Table $table): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Restaurant $restaurant, Table $table): bool
    {
        return false;
    }
}
