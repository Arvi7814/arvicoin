<?php
declare(strict_types=1);

namespace App\Telegram;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class FakeMedia extends Media
{
    public function getFullUrl(string $conversionName = ''): string
    {
        return 'https://variety.com/wp-content/uploads/2021/06/TikTok-Jump.png?w=970';
    }
}
