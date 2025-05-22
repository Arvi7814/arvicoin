<?php

namespace App\Filament\Resources\Shop;

use App\Enum\RoleEnum;
use App\Filament\Resources\Shop\BannerResource\Pages;
use App\Helpers\LangHelper;
use App\Models\Shop\Banner;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';

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
                Select::make('product_id')
                    ->label(trans('fields.product'))
                    ->relationship('product', 'name')
                    ->columnSpan(12)
                    ->requiredWithout('tag_id'),
                Select::make('tag_id')
                    ->label(trans('fields.tag'))
                    ->relationship('tag', 'name')
                    ->columnSpan(12)
                    ->requiredWithout('product_id')
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
                Tables\Columns\TextColumn::make('description')
                    ->label(trans('fields.description'))
                    ->words(10),
                Tables\Columns\BadgeColumn::make('product.name')
                    ->label(trans('fields.product')),
                Tables\Columns\BadgeColumn::make('tag.name')
                    ->label(trans('fields.tag')),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.banner');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.banners');
    }
}
