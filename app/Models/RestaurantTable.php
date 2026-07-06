<?php

namespace App\Models;

use App\Traits\RestaurantScoped;
use Database\Factories\RestaurantTableFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    /** @use HasFactory<RestaurantTableFactory> */
    use HasFactory, RestaurantScoped;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RestaurantTable $table) {
            if (empty($table->qr_code)) {
                $table->qr_code = (string) Str::uuid();
            }
        });

        static::created(function (RestaurantTable $table) {
            $table->generateQrCode();
        });
    }

    public function generateQrCode(): void
    {
        $url = route('public.order.form', $this->qr_code);

        $result = (new Builder(
            writer: new PngWriter,
            data: $url,
            size: 300,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
        ))->build();

        $directory = storage_path('app/public/qrcodes');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'table-'.$this->id.'.png';
        $filePath = $directory.'/'.$filename;

        file_put_contents($filePath, $result->getString());

        $this->update(['qr_code_image' => 'qrcodes/'.$filename]);
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        if (! $this->qr_code_image) {
            return null;
        }

        return Storage::disk('public')->url($this->qr_code_image);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
