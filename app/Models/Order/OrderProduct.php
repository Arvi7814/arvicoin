<?php

namespace App\Models\Order;

use App\Casts\OrderProductPriceCast;
use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $count
 * @property array $prices
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read float $price
 *
 * @property-read Product $product
 * @property-read Order $order
 */
class OrderProduct extends Model
{
    protected $guarded = [];

    protected $casts = [
        'prices' => 'json',
        'price' => OrderProductPriceCast::class
    ];

    /**
     * @return BelongsTo<Product, OrderProduct>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    /**
     * @return BelongsTo<Order, OrderProduct>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
