<?php

namespace App\Casts;

use App\Enum\CurrencyEnum;
use App\Models\Order\OrderProduct;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class OrderProductPriceCast implements CastsAttributes
{
    /**
     * @param OrderProduct $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        $currency = $model->order->currency ?? CurrencyEnum::USD->value;
        return (float)$model->prices[$currency] ?? 0;
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
