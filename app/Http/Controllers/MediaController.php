<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function __invoke(string $id)
    {
        return redirect(
            Media::query()->where('id', $id)->first()->getFullUrl()
        );
    }
}
