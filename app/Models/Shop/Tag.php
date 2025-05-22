<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $tg_color
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product[] $products
 */
class Tag extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected $perPage = 15;

    /** @var string[] */
    public array $translatable = ['name'];

    /**
     * @return BelongsToMany<Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
