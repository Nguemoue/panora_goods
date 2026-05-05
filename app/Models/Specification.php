<?php

namespace App\Models;

use App\Enums\SpecificationInputType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specification extends Model
{
    /** @use HasFactory<\Database\Factories\SpecificationFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'input_type' => SpecificationInputType::class,
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_specification')
            ->withPivot('is_required')
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_specification')
            ->withPivot('value')
            ->withTimestamps();
    }
}
