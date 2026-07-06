<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RestaurantScoped
{
    public function scopeForRestaurant(Builder $query): Builder
    {
        if (auth()->check() && !static::isAdmin()) {
            return $query->where('restaurant_id', auth()->id());
        }

        return $query;
    }

    public static function isAdmin(): bool
    {
        return auth()->check() && (auth()->user()->user?->is_admin ?? false);
    }

    public function belongsToCurrentRestaurant(): bool
    {
        return $this->restaurant_id === auth()->id();
    }
}
