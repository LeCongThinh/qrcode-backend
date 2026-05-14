<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
