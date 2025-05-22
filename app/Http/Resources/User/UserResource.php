<?php

namespace App\Http\Resources\User;

use App\Models\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read User $resource
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'phone_number' => $this->resource->phone_number,
            'language' => $this->resource->language,
            'latitude' => $this->resource->latitude,
            'longitude' => $this->resource->longitude
        ];
    }
}
