<?php

namespace App\Http\Resources\Shop;

use App\Models\Shop\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Product $resource
 */
class ProductResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'sale_count' => $this->resource->sale_count,
            'prices' => $this->resource->prices,
            'viewed' => $this->resource->viewed,
            'tiktok_product' => $this->resource->tiktok_product,
            'pubg_product' => $this->resource->pubg_product,
            'coverage' => $this->resource->mediaToConversionUrls('coverage')->first(),
            'images' => $this->resource->mediaToConversionUrls('images'),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
