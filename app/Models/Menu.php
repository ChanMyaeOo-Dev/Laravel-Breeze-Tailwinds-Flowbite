<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    protected $guarded = [];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
