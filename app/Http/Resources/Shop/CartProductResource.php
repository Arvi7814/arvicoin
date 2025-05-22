<?php

namespace App\Http\Resources\Shop;

use App\Models\Shop\CartProduct;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read CartProduct $resource
 */
class CartProductResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'count' => $this->resource->count,
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
