<?php

namespace App\Filament\Resources\System;

use App\Enum\RoleEnum;
use App\Enum\SettingsEnum;
use App\Helpers\LangHelper;
use App\Models\System\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

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
                Textarea::make('value')
                    ->label(trans('fields.value'))
                    ->hidden(static fn(Setting $record) => $record->isTranslatable())
                    ->columnSpan(12),
                LangHelper::input('translations')
                    ->formatStateUsing(
                        static fn(Setting $record) => empty($record->translations)
                            ? LangHelper::default()
                            : $record->translations
                    )
                    ->label(trans('fields.translations'))
                    ->hidden(static fn(Setting $record) => !$record->isTranslatable())
                    ->columnSpan(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(trans('fields.type'))
                    ->enum(SettingsEnum::options()),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(
                        static fn(Setting $record) => $record->isTranslatable()
                            ? $record->translation()
                            : $record->value
                    )
                    ->limit(50)
                    ->label(trans('fields.value')),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => SettingResource\Pages\ListSettings::route('/'),
            'edit' => SettingResource\Pages\EditSetting::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.setting');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.settings');
    }
}
