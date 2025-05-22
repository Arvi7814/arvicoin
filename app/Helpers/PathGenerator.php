<?php

declare(strict_types=1);

namespace App\Helpers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PathGenerator implements \Spatie\MediaLibrary\Support\PathGenerator\PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getRootPath($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getRootPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getRootPath($media).'responsive-images/';
    }

    private function getRootPath(Media $media): string
    {
        return md5($media->model_id.$media->model_type).'/';
    }
}
