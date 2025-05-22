<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Shop\ProductResource;
use App\Models\Order\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read OrderProduct $resource
 */
class OrderProductResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'count' => $this->resource->count,
            'prices' => $this->resource->prices,
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
