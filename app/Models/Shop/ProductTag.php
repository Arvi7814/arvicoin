<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductTag extends Model
{
    protected $guarded = [];
}
