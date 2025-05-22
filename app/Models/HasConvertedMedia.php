<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\ConversionEnum;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasConvertedMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(Media $media = null): void
    {
        foreach ($this->conversions() as $conversion => $size) {
            $this->addMediaConversion($conversion)
                ->height($size);
        }
    }

    public function mediaToConversionUrls(string $collection): Collection
    {
        $conversions = $this->conversions();

        return $this->getMedia($collection)
            ->map(
                function (Media $media) use ($conversions) {
                    $urls = ['og' => $media->getFullUrl()];

                    foreach ($conversions as $conversion => $size) {
                        $urls[$conversion] = $media->getFullUrl($conversion);
                    }

                    return $urls;
                }
            );
    }

    public function redirectedMediaToConversionUrls(string $collection): Collection
    {
        $conversions = $this->conversions();

        return $this->getMedia($collection)
            ->map(
                function (Media $media) use ($conversions) {
                    $urls = ['og' => route('media.show', ['id' => $media->id])];

                    foreach ($conversions as $conversion => $size) {
                        $urls[$conversion] = route('media.show', ['id' => $media->id]);
                    }

                    return $urls;
                }
            );
    }

    /**
     * @return array<string, int>
     */
    private static function conversions(): array
    {
        return [
            ConversionEnum::MD->value => 600,
            ConversionEnum::SM->value => 400,
            ConversionEnum::XS->value => 100,
        ];
    }
}
