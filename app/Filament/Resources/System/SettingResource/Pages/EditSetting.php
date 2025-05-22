<?php

namespace App\Filament\Resources\System\SettingResource\Pages;

use App\Filament\Resources\System\SettingResource;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }
}
