<?php

namespace App\Filament\Resources\Chat;

use App\Enum\OrderStatusEnum;
use App\Enum\RoleEnum;
use App\Filament\Resources\Chat\ChatResource\Pages;
use App\Models\Chat\Chat;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChatResource extends Resource
{
    protected static ?string $model = Chat::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat';

    protected static function getNavigationGroup(): string
    {
        return trans('fields.shop');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = Chat::query()
            ->whereHas('order')
            ->with(['order', 'order.user']);

        if (!Auth::user()->hasAnyRole([
            RoleEnum::MANAGER->value
        ])) {
            $query = $query->whereMember(Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        $statuses = OrderStatusEnum::options();

        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->label(trans('fields.order_number'))
                    ->formatStateUsing(fn(Chat $record) => "#{$record->order->id}")
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('order.user.first_name')
                    ->label(trans('fields.user'))
                    ->formatStateUsing(fn(Chat $record) => "# {$record->order->user->last_name} {$record->order->user->first_name}")
                    ->url(fn(Chat $record) => route('filament.resources.chat/chats.view', ['record' => $record->id]))
                    ->searchable(),
                BadgeColumn::make('order.status')
                    ->label(trans('fields.status'))
                    ->formatStateUsing(fn(Chat $record) => $statuses[$record->order->status->value]),
                BadgeColumn::make('from_telegram')
                    ->label(trans('fields.order_place'))
                    ->formatStateUsing(
                        fn(Chat $record) => $record->order->from_telegram
                            ? trans('fields.bot')
                            : trans('fields.app')
                    )
            ])
            ->filters([
                TernaryFilter::make('from_telegram')
                    ->trueLabel(trans('fields.bot'))
                    ->falseLabel(trans('fields.app'))
                    ->label(trans('fields.order_place'))
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('order', static function (Builder $builder) {
                            return $builder->where('from_telegram', true);
                        }),
                        false: fn(Builder $query) => $query->whereHas('order', static function (Builder $builder) {
                            return $builder->where('from_telegram', false);
                        }),
                        blank: fn(Builder $query) => $query
                    )
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChats::route('/'),
//            'create' => Pages\CreateChat::route('/create'),
//            'edit' => Pages\EditChat::route('/{record}/edit'),
            'view' => Pages\ViewChat::route('/{record}'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans('fields.chat');
    }

    public static function getPluralLabel(): ?string
    {
        return trans('fields.chats');
    }
}
