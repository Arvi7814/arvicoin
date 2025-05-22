<?php

namespace App\Http\Controllers\Api\Shop;

use App\Enum\BannerTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\BannerResource;
use App\Models\Query\BannerQuery;
use App\Models\Shop\Banner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    public function notifications(): AnonymousResourceCollection
    {
        return BannerResource::collection(
            $this->query(BannerTypeEnum::NOTIFICATION)
                ->get()
        );
    }

    public function slides(): AnonymousResourceCollection
    {
        return BannerResource::collection(
            $this->query(BannerTypeEnum::SLIDE)
                ->get()
        );
    }

    private function query(BannerTypeEnum $bannerTypeEnum): BannerQuery
    {
        return Banner::query()
            ->with(['tag', 'product']);
    }
}
