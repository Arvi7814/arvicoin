<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Shop\CartProductResource;
use App\Http\Resources\User\UserResource;
use App\Models\Order\Order;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Order $resource
 */
class OrderResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status,
            'tiktok_login' => $this->resource->tiktok_login,
            'tiktok_password' => $this->resource->tiktok_password,
            'pubg_id' => $this->resource->pubg_id,
            'currency' => $this->resource->currency,
            'chat' => ChatResource::make($this->whenLoaded('chat')),
            'operator' => UserResource::make($this->whenLoaded('operator')),
            'products' => CartProductResource::collection($this->whenLoaded('orderProducts')),
        ];
    }
}
