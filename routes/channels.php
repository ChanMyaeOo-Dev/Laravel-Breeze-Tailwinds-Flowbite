<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('restaurant.{restaurantId}.kitchen', function ($user, $restaurantId) {
    return (int) $user->id === (int) $restaurantId || ($user->user?->is_admin ?? false);
});
