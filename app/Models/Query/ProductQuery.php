<?php

declare(strict_types=1);

namespace App\Models\Query;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Product>
 */
class ProductQuery extends Builder
{
    public function orderByViews(): self
    {
        return $this->orderByDesc('viewed');
    }

    /**
     * @param  int[]  $tags
     * @return $this
     */
    public function whereHasTags(array $tags): self
    {
        return $this->whereHas(
            'tags',
            fn (Builder $builder) => $builder->whereIn('tags.id', $tags)
        );
    }

    public function search(string $query): self
    {
        return $this->where('name', 'like', "%$query%");
    }
}
