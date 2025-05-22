<?php

namespace App\Http\Resources\System;

use App\Models\System\Setting;
use App\Models\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Setting $resource
 */
class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => $this->resource->type->value,
            'value' => $this->resource->value,
            'translations' => $this->resource->translations
        ];
    }
}
