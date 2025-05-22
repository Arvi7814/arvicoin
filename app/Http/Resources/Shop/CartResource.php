<?php

namespace App\Http\Resources\Shop;

use App\Models\Shop\Cart;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Cart $resource
 */
class CartResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'products' => CartProductResource::collection($this->whenLoaded('cartProducts')),
        ];
    }
}
