<?php

namespace App\Models\Shop;

use App\Enum\BannerTypeEnum;
use App\Models\HasConvertedMedia;
use App\Models\Query\BannerQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property BannerTypeEnum $type
 * @property int|null $product_id
 * @property int|null $tag_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product|null $product
 * @property-read Tag|null $tag
 *
 * @method static BannerQuery query()
 */
class Banner extends Model implements HasMedia
{
    use HasConvertedMedia, HasTranslations;

    protected $with = ['media'];
    protected $guarded = [];

    protected $perPage = 15;

    protected $casts = [
        'type' => BannerTypeEnum::class,
    ];

    /** @var string[] */
    protected $translatable = ['name', 'description'];

    public function newEloquentBuilder($query): BannerQuery
    {
        return new BannerQuery($query);
    }

    /**
     * @return BelongsTo<Product, Banner>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return BelongsTo<Tag, Banner>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
