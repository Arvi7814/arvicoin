<?php

namespace App\Filament\Resources\System;

use App\Enum\RoleEnum;
use App\Filament\Resources\AnnounceResource\RelationManagers;
use App\Filament\Resources\System\AnnounceResource\Pages;
use App\Helpers\LangHelper;
use App\Jobs\User\SendAnnounceJob;
use App\Models\Announce;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AnnounceResource extends Resource
{
    protected static ?string $model = Announce::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function can(string $action, ?Model $record = null): bool
    {
        if (!Auth::user()->hasAnyRole([
            RoleEnum::MANAGER->value,
        ])) {
            return false;
        }

        return parent::can($action, $record);
    }

    protected static function getNavigationGroup(): string
    {
        return trans('fields.system');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                LangHelper::input('title')
                    ->label(trans('fields.name'))
                    ->columnSpan(12)
                    ->required(),
                LangHelper::tabs('content')
                    ->label(trans('fields.message'))
                    ->columnSpan(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(trans('fields.name')),
                Tables\Columns\TextColumn::make('content')
                    ->label(trans('fields.message')),
                Tables\Columns\TextColumn::make('content')
                    ->label(trans('fields.message')),
                Tables\Columns\TextColumn::make('sent')
                    ->label(trans('fields.sent'))
                    ->formatStateUsing(static function (Announce $record) {
                        return "{$record->sentUsers()->count()}/{$record->users()->count()}";
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label(trans('fields.send'))
                    ->action(static function (Announce $record) {
                        SendAnnounceJob::dispatch($record->id);

                        Notification::make('sent')
                            ->success()
                            ->title(trans('fields.sent'))
                            ->send();
                    })
                    ->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnounces::route('/'),
            'create' => Pages\CreateAnnounce::route('/create'),
            'edit' => Pages\EditAnnounce::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.announce');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.announces');
    }
}
