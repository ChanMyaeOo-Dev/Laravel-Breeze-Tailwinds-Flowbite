<?php

namespace App\Models;

use App\Traits\RestaurantScoped;
use Database\Factories\MenuCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    /** @use HasFactory<MenuCategoryFactory> */
    use HasFactory, RestaurantScoped;

    protected $guarded = [];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
