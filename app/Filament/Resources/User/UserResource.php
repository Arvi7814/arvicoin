<?php

namespace App\Filament\Resources\User;

use App\Enum\RoleEnum;
use App\Helpers\PhoneNumberHelper;
use App\Models\User\User;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Phpsa\FilamentPasswordReveal\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static function getNavigationGroup(): string
    {
        return trans('fields.system');
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if (!Auth::user()->hasAnyRole([
            RoleEnum::MANAGER->value,
        ])) {
            return false;
        }

        return parent::can($action, $record);
    }

    public static function getEloquentQuery(): Builder
    {
        return User::query()
            ->whereHas(
                'roles',
                fn(Builder $query) => $query->whereNot('name', RoleEnum::CUSTOMER->value)
            );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label(trans('fields.first_name'))
                    ->required()
                ->columnSpan(12),
                Forms\Components\TextInput::make('last_name')
                    ->label(trans('fields.last_name'))
                    ->required()
                ->columnSpan(12),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->telRegex(PhoneNumberHelper::regex())
                    ->mask(
                        fn(Forms\Components\TextInput\Mask $mask) => PhoneNumberHelper::mask($mask)
                    )
                    ->label(trans('fields.phone_number'))
                    ->required()
                ->columnSpan(12),
                Forms\Components\Select::make('roles')
                    ->label(trans('fields.role'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->required()
                    ->columnSpan(12),
                Password::make('password')
                    ->label(trans('fields.password'))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(Page $livewire) => ($livewire instanceof CreateRecord))
                    ->required()
                    ->columnSpan(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label(trans('fields.first_name')),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(trans('fields.last_name')),
                Tables\Columns\BadgeColumn::make('phone_number')
                    ->label(trans('fields.phone_number')),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label(trans('fields.role')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('roles')
                    ->label(trans('fields.role'))
                    ->relationship('roles', 'name'),
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
            'index' => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.user');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.users');
    }
}
