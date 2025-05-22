<?php

namespace App\Observers\Shop;

use App\Models\Shop\Banner;

class BannerObserver
{
    public $afterCommit = true;

    public function created(Banner $banner): void
    {

    }
}
