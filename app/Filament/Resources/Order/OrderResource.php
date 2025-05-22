<?php

namespace App\Filament\Resources\Order;

use App\Enum\RoleEnum;
use App\Filament\Resources\Order\OrderResource\Pages;
use App\Filament\Resources\Order\OrderResource\RelationManagers\OrderProductsRelationManager;
use App\Models\Order\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static function getNavigationGroup(): string
    {
        return trans('fields.shop');
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if (!Auth::user()->hasAnyRole([
            RoleEnum::MODERATOR->value,
            RoleEnum::MANAGER->value,
        ])) {
            return false;
        }

        return parent::can($action, $record);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->label(trans('fields.order_number'))
                    ->columnSpan(12),
                Select::make('user_id')
                    ->label(trans('fields.user'))
                    ->relationship('user', 'first_name')
                    ->columnSpan(12),
                Select::make('operator_id')
                    ->label(trans('fields.operator'))
                    ->relationship('operator', 'first_name')
                    ->columnSpan(12),
                TextInput::make('currency')
                    ->label(trans('fields.ordered_currency'))
                    ->columnSpan(12),
                TextInput::make('tiktok_login')
                    ->label(trans('fields.tiktok_login'))
                    ->columnSpan(12),
                TextInput::make('tiktok_password')
                    ->label(trans('fields.tiktok_password'))
                    ->columnSpan(12),
                TextInput::make('pubg_id')
                    ->label(trans('fields.pubg_id'))
                    ->columnSpan(12)
                    ->hidden()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
//            'create' => Pages\CreateOrder::route('/create'),
//            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}')
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.order');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.orders');
    }
}
