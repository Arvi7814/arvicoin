<?php

namespace App\Filament\Resources\Shop\TagResource\Pages;

use App\Filament\Resources\Shop\TagResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
