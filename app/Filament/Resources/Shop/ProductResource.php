<?php

namespace App\Filament\Resources\Shop;

use App\Enum\ProductStatusEnum;
use App\Enum\RoleEnum;
use App\Filament\Resources\Shop\ProductResource\Pages;
use App\Filament\Resources\Shop\ProductResource\RelationManagers\TagsRelationManager;
use App\Helpers\LangHelper;
use App\Helpers\PriceHelper;
use App\Models\Shop\Product;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static function getNavigationGroup(): string
    {
        return trans('fields.shop');
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if (!Auth::user()->hasAnyRole([
            RoleEnum::MANAGER->value,
            RoleEnum::ADMIN->value,
        ])) {
            return false;
        }

        return parent::can($action, $record);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('coverage')
                    ->label(trans('fields.coverage'))
                    ->collection('coverage')
                    ->columnSpan(12)
                    ->required(),
                LangHelper::input('name')
                    ->label(trans('fields.name'))
                    ->columnSpan(12)
                    ->required(),
                LangHelper::input('description')
                    ->label(trans('fields.description'))
                    ->columnSpan(12)
                    ->required(),
                PriceHelper::input('prices')
                    ->label(trans('fields.price'))
                    ->columnSpan(12)
                    ->required(),
                TextInput::make('sale_count')
                    ->label(trans('fields.sale_count'))
                    ->mask(fn(TextInput\Mask $mask) => $mask->numeric())
                    ->required()
                    ->columnSpan(12),
                Radio::make('status')
                    ->label(trans('fields.status'))
                    ->options(ProductStatusEnum::options())
                    ->columnSpan(12),
                Toggle::make('tiktok_product')
                    ->label(trans('fields.tiktok_product'))
                    ->columnSpan(12),
                Toggle::make('pubg_product')
                    ->label(trans('fields.pubg_product'))
                    ->hidden()
                    ->default(false)
                    ->columnSpan(12),
                SpatieMediaLibraryFileUpload::make('images')
                    ->label(trans('fields.images'))
                    ->collection('images')
                    ->multiple()
                    ->columnSpan(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('coverage')
                    ->label(trans('fields.coverage'))
                    ->collection('coverage'),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('fields.name')),
                Tables\Columns\TextColumn::make('price')
                    ->label(trans('fields.price')),
                Tables\Columns\TextColumn::make('description')
                    ->label(trans('fields.description'))
                    ->words(10),
                Tables\Columns\BadgeColumn::make('viewed')
                    ->label(trans('fields.viewed')),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(trans('fields.status'))
                    ->enum(ProductStatusEnum::options()),
                Tables\Columns\BadgeColumn::make('createdBy.first_name')
                    ->label(trans('fields.created_by')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->label(trans('fields.status'))
                    ->options(ProductStatusEnum::options()),
                SelectFilter::make('created_by')
                    ->label(trans('fields.created_by'))
                    ->relationship('createdBy', 'first_name'),
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
            TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.product');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.products');
    }
}
