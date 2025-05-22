<?php

namespace App\Filament\Resources\Shop;

use App\Enum\RoleEnum;
use App\Filament\Resources\Shop\CustomerResource\Pages;
use App\Filament\Resources\Shop\CustomerResource\RelationManagers\CartRelationManager;
use App\Helpers\PhoneNumberHelper;
use App\Models\Shop\Customer;
use App\Models\User\User;
use App\Notifications\User\PushMessage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static function getNavigationGroup(): string
    {
        return trans('fields.shop');
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

    public static function getSlug(): string
    {
        return 'shop/customers';
    }

    public static function getEloquentQuery(): Builder
    {
        return User::query()
            ->whereHas(
                'roles',
                fn(Builder $query) => $query->where('name', RoleEnum::CUSTOMER->value)
            );
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label(trans('fields.first_name'))
                    ->required()
                    ->columnSpan(12),
                TextInput::make('last_name')
                    ->label(trans('fields.last_name'))
                    ->required()
                    ->columnSpan(12),
                TextInput::make('phone_number')
                    ->label(trans('fields.phone_number'))
                    ->tel()
                    ->telRegex(PhoneNumberHelper::regex())
                    ->mask(
                        fn(Mask $mask) => PhoneNumberHelper::mask($mask)
                    )
                    ->required()
                    ->columnSpan(12),
                ViewField::make('location')
                    ->label(trans('fields.location'))
                    ->view('filament.forms.components.map')
                    ->columnSpan(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label(trans('fields.first_name'))
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label(trans('fields.last_name'))
                    ->searchable(),
                TextColumn::make('username')
                    ->label(trans('fields.username'))
                    ->url(static fn(User $record) => $record->username
                        ? "https://t.me/$record->username"
                        : false)
                    ->openUrlInNewTab()
                    ->searchable(),
                BadgeColumn::make('phone_number')
                    ->label(trans('fields.phone_number'))
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label(trans('fields.status'))
                    ->formatStateUsing(static fn(User $record) => $record->status->getLabel())
                    ->color(static fn(User $record) => $record->status->getColor()),
                BadgeColumn::make('has_telegram')
                    ->formatStateUsing(static fn(User $record) => $record->chat_id
                        ? trans('fields.has')
                        : trans('fields.no'))
                    ->label(trans('fields.has_telegram'))
            ])
            ->filters([
                TernaryFilter::make('has_telegram')
                    ->trueLabel(trans('fields.has'))
                    ->falseLabel(trans('fields.no'))
                    ->label(trans('fields.has_telegram'))
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('chat_id'),
                        false: fn(Builder $query) => $query->whereNull('chat_id'),
                        blank: fn(Builder $query) => $query
                    )
            ])
            ->actions([
                Action::make('send-notification')
                    ->label(trans('fields.send-notification'))
                    ->form([
                        TextInput::make('title')
                            ->label(trans('fields.name'))
                            ->required()
                            ->columnSpan(12),
                        Textarea::make('message')
                            ->label(trans('fields.message'))
                            ->required()
                            ->columnSpan(12)
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->notify(new PushMessage(
                            title: $data['title'],
                            body: $data['message']
                        ));

                        Notification::make('sent')
                            ->success()
                            ->title(trans('fields.sent'))
                            ->send();
                    }),
                ViewAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CartRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
//            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
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
