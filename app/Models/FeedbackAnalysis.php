<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackAnalysis extends Model
{
    protected $fillable = [
        'feedback_id',
        'sentiment',
        'confidence',
        'probabilities',
        'categories',
        'keywords',
        'summary',
        'model_version',
    ];

    protected $casts = [
        'probabilities' => 'array',
        'categories' => 'array',
        'keywords' => 'array',
        'confidence' => 'float',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
