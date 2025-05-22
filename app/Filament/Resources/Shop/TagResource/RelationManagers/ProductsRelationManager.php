<?php

namespace App\Filament\Resources\Shop\TagResource\RelationManagers;

use App\Filament\Resources\Shop\ProductResource;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return ProductResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return ProductResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getModelLabel(): string
    {
        return trans('fields.product');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('fields.products');
    }
}
