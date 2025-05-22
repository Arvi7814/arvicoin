<?php

namespace App\Filament\Resources\System;

use App\Enum\RoleEnum;
use App\Filament\Resources\System\TemplateMessageResource\RelationManagers;
use App\Helpers\LangHelper;
use App\Models\TemplateMessage;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TemplateMessageResource extends Resource
{
    protected static ?string $model = TemplateMessage::class;

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
                TextInput::make('number')
                    ->label(trans('fields.number'))
                    ->numeric()
                    ->columnSpan(12)
                    ->required(),
                LangHelper::input('message')
                    ->label(trans('fields.message'))
                    ->columnSpan(12)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(trans('fields.number')),
                Tables\Columns\TextColumn::make('message')
                    ->label(trans('fields.message'))
                    ->formatStateUsing(static fn(TemplateMessage $record) => $record->message[app()->getLocale()] ?? '')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => TemplateMessageResource\Pages\ListTemplateMessages::route('/'),
            'create' => TemplateMessageResource\Pages\CreateTemplateMessage::route('/create'),
            'edit' => TemplateMessageResource\Pages\EditTemplateMessage::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.template');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.templates');
    }
}
