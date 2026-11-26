<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\ProductImageOptimizer;
use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected $fillable = ['product_id', 'path', 'thumb_path', 'type', 'is_primary'];

    protected static function booted(): void
    {
        static::saved(function (ProductImage $image): void {
            if ($image->is_primary && $image->product_id) {
                self::query()
                    ->where('product_id', $image->product_id)
                    ->whereKeyNot($image->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            if (filled($image->path) && ($image->wasRecentlyCreated || $image->wasChanged('path') || blank($image->thumb_path))) {
                app(ProductImageOptimizer::class)->generateThumbnail($image);
            }
        });
    }

    public function getUrlAttribute(): string
    {
        if (blank($this->path)) {
            return asset('images/cart-placeholder.jpg');
        }

        return asset('storage/'.ltrim((string) $this->path, '/'));
    }

    public function getThumbUrlAttribute(): string
    {
        if (filled($this->thumb_path)) {
            return asset('storage/'.ltrim((string) $this->thumb_path, '/'));
        }

        return $this->url;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
