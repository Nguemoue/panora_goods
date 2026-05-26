<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory;

    protected $fillable = ['product_id', 'path', 'type', 'is_primary'];


    public function getUrlAttribute(): string
    {
        if (!Storage::disk('public')->exists($this->path)) {
            return asset('images/cart-placeholder.jpg');
        }
        return  asset('storage/' . $this->path);
    }
    public function product(): BelongsTo
    {

        return $this->belongsTo(Product::class);
    }
}
