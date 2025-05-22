<?php

namespace App\Filament\Resources\Shop;

use App\Enum\RoleEnum;
use App\Filament\Resources\Shop\TagResource\Pages;
use App\Filament\Resources\Shop\TagResource\RelationManagers\ProductsRelationManager;
use App\Helpers\LangHelper;
use App\Models\Shop\Tag;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
                LangHelper::input('name')
                    ->label(trans('fields.name'))
                    ->required(),
                Section::make('color')
                    ->label(trans('fields.color'))
                    ->schema([
                        ColorPicker::make('color')
                            ->label(trans('fields.color'))
                            ->required(),
                        ColorPicker::make('tg_color')
                            ->label(trans('fields.tg_color'))
                            ->required(),
                    ])
                    ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('fields.name')),
                Tables\Columns\ColorColumn::make('color')
                    ->label(trans('fields.color')),
                Tables\Columns\ColorColumn::make('tg_color')
                    ->label(trans('fields.tg_color')),
            ])
            ->filters([
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.tag');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.tags');
    }
}
