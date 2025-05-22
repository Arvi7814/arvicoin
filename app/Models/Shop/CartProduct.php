<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Product $product
 */
class CartProduct extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo<Product, CartProduct>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
