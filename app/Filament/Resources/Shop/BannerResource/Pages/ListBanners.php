<?php

namespace App\Filament\Resources\Shop\BannerResource\Pages;

use App\Filament\Resources\Shop\BannerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
