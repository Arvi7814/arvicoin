<?php
declare(strict_types=1);

namespace App\Http\Services\Chat;

use Illuminate\Http\UploadedFile;

class Message
{
    public function __construct(
        public readonly ?string       $message,
        public readonly ?UploadedFile $media
    )
    {
    }
}
