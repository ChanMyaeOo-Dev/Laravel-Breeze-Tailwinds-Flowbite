<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackFactory> */
    use HasFactory;

    protected $table = "feedback";

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'id', 'restaurant_id');
    }
}
