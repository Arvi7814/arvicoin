<?php

namespace App\Filament\Resources\Shop\ProductResource\RelationManagers;

use App\Filament\Resources\Shop\TagResource;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return TagResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return TagResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected static function getModelLabel(): string
    {
        return trans('fields.tag');
    }

    protected static function getPluralModelLabel(): string
    {
        return trans('fields.tags');
    }
}
