<?php

namespace App\Filament\Resources\Chat\ChatResource\Pages;

use App\Filament\Resources\Chat\ChatResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChat extends CreateRecord
{
    protected static string $resource = ChatResource::class;
}
