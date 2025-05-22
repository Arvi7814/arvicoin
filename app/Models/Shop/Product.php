<?php

namespace App\Models\Shop;

use App\Casts\ProductPriceCast;
use App\Enum\ProductStatusEnum;
use App\Models\HasConvertedMedia;
use App\Models\Order\OrderProduct;
use App\Models\Query\ProductQuery;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $sale_count
 * @property array $prices
 * @property ProductStatusEnum $status
 * @property int $viewed
 * @property boolean $tiktok_product
 * @property boolean $pubg_product
 * @property int $created_by
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read float $price
 *
 * @property-read User $createdBy
 * @property-read Tag[] $tags
 * @property-read Collection<ProductView> $views
 * @property-read Collection<CartProduct> $cartProducts
 * @property-read Collection<OrderProduct> $orderProducts
 *
 * @method static ProductQuery query()
 */
class Product extends Model implements HasMedia
{
    use HasTranslations, SoftDeletes, HasConvertedMedia;

    protected $with = ['media'];
    protected $guarded = [];

    protected $perPage = 15;

    /*** @var string[] $translatable */
    public array $translatable = ['name', 'description'];

    protected $casts = [
        'sale_count' => 'int',
        'prices' => 'json',
        'tiktok_product' => 'boolean',
        'pubg_product' => 'boolean',
        'price' => ProductPriceCast::class,
        'status' => ProductStatusEnum::class,
    ];

    public function newEloquentBuilder($query): ProductQuery
    {
        return new ProductQuery($query);
    }

    /**
     * @return BelongsTo<User, Product>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    /**
     * @return HasMany<ProductView>
     */
    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class, 'product_id');
    }

    /**
     * @return HasMany<CartProduct>
     */
    public function cartProducts(): HasMany
    {
        return $this->hasMany(CartProduct::class, 'product_id');
    }

    /**
     * @return HasMany<OrderProduct>
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'product_id');
    }
}
