<?php

namespace App\Filament\Resources\Order\OrderResource\Pages;

use App\Enum\DeletedStatusEnum;
use App\Enum\OrderStatusEnum;
use App\Enum\RoleEnum;
use App\Events\Chat\OrderClosedEvent;
use App\Events\Order\OrderTakenEvent;
use App\Filament\Resources\Order\OrderResource;
use App\Http\Services\Order\OrderService;
use App\Jobs\Order\DeleteOrderJob;
use App\Models\Chat\Chat;
use App\Models\Order\Order;
use App\Models\Query\OrderQuery;
use App\Models\User\User;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use InvadersXX\FilamentKanbanBoard\Pages\FilamentKanbanBoard;

class ListOrders extends FilamentKanbanBoard
{
    public bool $sortable = true;
    public bool $sortableBetweenStatuses = true;

    protected static string $resource = OrderResource::class;

    public static function route(string $path): array
    {
        return [
            'class' => static::class,
            'route' => $path,
        ];
    }

    protected function styles(): array
    {
        return [
            'wrapper' => 'w-full h-full flex space-x-4 overflow-x-auto',
            'kanbanWrapper' => 'h-full flex-1',
            'kanban' => 'px-2 flex flex-col h-full',
            'kanbanHeader' => 'px-4 py-2 mb-2 bg-primary-500 text-sm text-white text-center font-medium rounded',
            'kanbanFooter' => '',
            'kanbanRecords' => 'space-y-2 flex-1 overflow-y-auto',
            'record' => 'shadow text-sm rounded border',
            'recordContent' => 'w-full',
        ];
    }

    protected function statuses(): Collection
    {
        $statuses = collect();
        foreach (OrderStatusEnum::options() as $option => $label) {
            $statuses->push([
                'id' => $option,
                'title' => $label
            ]);
        }
        return $statuses;
    }

    protected function getActions(): array
    {
        return array_merge(
            parent::getActions(), [
                Action::make('delete-all')
                    ->label(trans('fields.delete-all'))
                    ->action(function () {
                        if (!$this->canDeleteAll()) {
                            abort(403);
                        }
                        $ids = Order::query()
                            ->whereStatus(OrderStatusEnum::CLOSED)
                            ->where(fn(OrderQuery $j) => $j
                                ->whereNull('deleted_status')
                                ->orWhereNot('deleted_status', DeletedStatusEnum::BY_MANAGER)
                            )
                            ->pluck('id');

                        foreach ($ids as $id) {
                            DeleteOrderJob::dispatch($id);
                        }

                        Notification::make()
                            ->title('Success')
                            ->body(trans('fields.added-queue'))
                            ->success()
                            ->send();
                    })
                    ->hidden(fn() => !$this->canDeleteAll())
                    ->requiresConfirmation()
            ]
        );
    }

    protected function records(): Collection
    {
        $user = Auth::user();
        $query = Order::query();

        if ($user->hasRole(RoleEnum::MANAGER->value)) {
            $query
                ->whereNull('deleted_status')
                ->orWhereNot('deleted_status', DeletedStatusEnum::BY_MANAGER);
        } else {
            $query = $query
                ->where(
                    fn(OrderQuery $q) => $q
                        ->whereStatus(OrderStatusEnum::OPENED)
                )
                ->orWhere(
                    fn(OrderQuery $q) => $q
                        ->whereNot('status', OrderStatusEnum::OPENED)
                        ->where('operator_id', $user->id)
                        ->where(fn(OrderQuery $j) => $j
                            ->whereNull('deleted_status')
                            ->orWhereNotIn('deleted_status', [
                                DeletedStatusEnum::BY_MODERATOR->value,
                                DeletedStatusEnum::BY_MANAGER->value,
                            ])
                        )
                );
        }

        return $query
            ->with(['chat'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'order' => $order,
                    'status' => $order->status->value,
                ];
            });
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds): void
    {
        $operatorId = Auth::id();
        /** @var Order $order */
        $order = Order::query()->where(['id' => $recordId])->first();

        if (!$order) {
            return;
        }

        DB::transaction(
            function () use ($statusId, $operatorId, $order) {
                if ($order->operator_id && $order->operator_id !== $operatorId) {
                    if (!Auth::user()->hasAnyRole([
                        RoleEnum::MANAGER->value
                    ])) {
                        throw new UnauthorizedException("Order belongs to another user");
                    }
                }

                $isBeforeClosed = $order->status === OrderStatusEnum::CLOSED;

                $order->status = $statusId;
                $order->operator_id = $operatorId;
                $order->closed_at = $statusId == OrderStatusEnum::CLOSED->value ? now() : null;
                $order->save();

                if (!$isBeforeClosed && $order->chat && $order->status === OrderStatusEnum::CLOSED) {
                    $service = new OrderService();
                    $service->closeOrder($order);

                    broadcast(new OrderClosedEvent($order->chat))->toOthers();
                }

                if (!$order->chat()->exists()) {
                    $chat = new Chat();
                    $chat->order_id = $order->id;
                    $chat->save();

                    $chat->chatMembers()->firstOrCreate(['user_id' => $order->user_id]);
                    $chat->chatMembers()->firstOrCreate(['user_id' => $order->operator_id]);

                    $managers = User::query()
                        ->whereHas(
                            'roles',
                            fn(Builder $query) => $query
                                ->where('name', RoleEnum::MANAGER->value)
                        )
                        ->get();

                    foreach ($managers as $manager) {
                        if ($manager->id !== $order->operator_id) {
                            $chat->chatMembers()->firstOrCreate(['user_id' => $manager->id]);
                        }
                    }

                    broadcast(new OrderTakenEvent())->toOthers();
                }
            }
        );

        $this->emit('$refresh');
    }

    protected function getListeners(): array
    {
        $id = Auth::id();
        return array_merge(
            parent::getListeners(),
            [
                'echo:orders,.order.created' => '$refresh',
                'echo:orders,.order.taken' => '$refresh',
                "echo:users.$id,.message.sent" => '$refresh',
            ]
        );
    }

    public function getTitle(): string
    {
        return trans('fields.orders_list');
    }

    private function canDeleteAll(): bool
    {
        return Auth::user()->hasAnyRole([
            RoleEnum::MANAGER->value
        ]);
    }
}
