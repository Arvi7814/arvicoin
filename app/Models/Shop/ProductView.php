<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ip_address
 * @property int $product_id
 * @property int|null $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductView extends Model
{
    protected $guarded = [];
}
