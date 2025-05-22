<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserResource;
use App\Models\Chat\ChatMessage;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ChatMessage $resource
 */
class ChatMessageResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $media = $this->resource->getFirstMedia();

        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'content' => $this->resource->content,
            'created_at' => $this->resource->created_at,
            'media' => $media ? [
                'url' => $media->getUrl(),
                'type' => $media->getTypeFromMime(),
                'mime' => $media->mime_type
            ] : null,
            'user' => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
