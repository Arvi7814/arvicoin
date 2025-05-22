<?php

namespace App\Http\Resources\Shop;

use App\Models\Shop\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Tag $resource
 */
class TagResource extends JsonResource
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
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
