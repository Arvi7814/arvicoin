<?php

namespace App\Filament\Resources\Shop\BannerResource\Pages;

use App\Filament\Resources\Shop\BannerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord
{
    protected static string $resource = BannerResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
