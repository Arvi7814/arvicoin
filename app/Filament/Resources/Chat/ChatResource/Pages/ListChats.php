<?php

namespace App\Filament\Resources\Chat\ChatResource\Pages;

use App\Filament\Resources\Chat\ChatResource;
use Filament\Resources\Pages\ListRecords;

class ListChats extends ListRecords
{
    protected static string $resource = ChatResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }
}
