<?php

namespace App\Filament\Resources\System\AnnounceResource\Pages;

use App\Filament\Resources\System\AnnounceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnounce extends EditRecord
{
    protected static string $resource = AnnounceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
