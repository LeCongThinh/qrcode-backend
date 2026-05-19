<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property numeric $price
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $qr_code_url
 * @property int $stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $slug
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereQrCodeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'price',
        'description',
        'image_url',
        'qr_code_url',
        'stock'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            // Tạo slug cơ bản
            $slug = Str::slug($product->name);
            $count = static::where('slug', 'like', $slug . '%')->count();
            $product->slug = $count ? "{$slug}-{$count}" : $slug;

            // Tạo SKU ngẫu nhiên và đảm bảo không trùng lặp
            do {
                $randomString = Str::upper(Str::random(12));
                $sku = 'MASP-' . $randomString;
            } while (static::where('sku', $sku)->exists());

            $product->sku = $sku;
        });
    }
}
