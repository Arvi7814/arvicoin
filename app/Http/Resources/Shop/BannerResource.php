<?php

namespace App\Http\Resources\Shop;

use App\Models\Shop\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Banner $resource
 */
class BannerResource extends JsonResource
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
            'tag_id' => $this->resource->tag_id,
            'product_id' => $this->resource->product_id,
            'images' => $this->resource->redirectedMediaToConversionUrls('coverage'),
        ];
    }
}
