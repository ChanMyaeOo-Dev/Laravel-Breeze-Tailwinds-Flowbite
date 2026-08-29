<?php

namespace App\Models;

use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    protected $table = 'feedback';

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'id', 'restaurant_id');
    }

    public function analysis()
    {
        return $this->hasOne(FeedbackAnalysis::class);
    }

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }
}
