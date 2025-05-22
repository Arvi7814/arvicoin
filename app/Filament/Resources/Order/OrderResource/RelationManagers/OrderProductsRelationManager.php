<?php

namespace App\Filament\Resources\Order\OrderResource\RelationManagers;

use App\Models\Order\OrderProduct;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(trans('fields.name')),
                Tables\Columns\TextColumn::make('count')
                    ->label(trans('fields.count')),
                Tables\Columns\TextColumn::make('price')
                    ->label(trans('fields.ordered_price')),
                Tables\Columns\TextColumn::make('amount')
                    ->label(trans('fields.amount'))
                    ->formatStateUsing(fn(OrderProduct $record) => $record->count * $record->price),
                Tables\Columns\BadgeColumn::make('product.status')
                    ->label(trans('fields.status')),
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return string|null
     */
    public static function getRecordLabel(): ?string
    {
        return trans('fields.product');
    }

    /**
     * @return string|null
     */
    public static function getPluralRecordLabel(): ?string
    {
        return trans('fields.products');
    }
}
