<?php
declare(strict_types=1);

namespace App\Helpers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MediaHelper
{
    public static function isPhoto(Media $media): bool
    {
        return in_array($media->mime_type, [
            'image/jpeg',
            'image/png'
        ]);
    }

    public static function isVideo(Media $media): bool
    {
        return in_array($media->mime_type, [
            'video/mp4',
            'image/png'
        ]);
    }

    public static function isAnimation(Media $media): bool
    {
        return in_array($media->mime_type, [
            'image/gif'
        ]);
    }

    public static function isAudio(Media $media): bool
    {
        return in_array($media->mime_type, [
            'audio/ogg',
            'audio/mpeg'
        ]);
    }
}
