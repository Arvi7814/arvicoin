<?php

namespace App\Filament\Resources\System\TemplateMessageResource\Pages;

use App\Filament\Resources\System\TemplateMessageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateMessages extends ListRecords
{
    protected static string $resource = TemplateMessageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
