<?php

namespace App\Casts;

use App\Enum\CurrencyEnum;
use App\Models\Shop\Product;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ProductPriceCast implements CastsAttributes
{
    /**
     * @param Product $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        return (float)($model->prices[CurrencyEnum::USD->value] ?? 0);
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
