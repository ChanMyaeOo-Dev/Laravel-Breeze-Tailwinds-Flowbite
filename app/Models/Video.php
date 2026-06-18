<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'original_name',
        's3_path',
        'file_size',
        'status',
    ];

    public function getUrlAttribute()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');
        return $disk->url($this->s3_path);
    }
}
