<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<CartProduct> $cartProducts
 * @property-read Collection<Product> $products
 */
class Cart extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany<CartProduct>
     */
    public function cartProducts(): HasMany
    {
        return $this->hasMany(CartProduct::class, 'cart_id');
    }

    /**
     * @return BelongsToMany<Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'cart_products',
            'cart_id',
            'product_id'
        );
    }
}
