<?php
/**
 * @var Order $order
 */

use App\Enum\OrderStatusEnum;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Builder;

$chat = $order->chat;
if ($chat) {
    $chat->loadcount(['unreadChatMessages' => fn(Builder $query) => $query->where('user_id', Auth::id())]);
}
?>


<article>
    <div class="px-4 py-1 text-white flex items-center justify-between bg-gray-600">
        <h2 class="mr-3">
            #{{$order->id}}. {{$order->user->last_name}} {{$order->user->first_name}}
        </h2>
        <ul class="flex items-center justify-end">
            <li>
                <a href="{{ route('filament.resources.order/orders.view', ['record' => $order->id]) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
            </li>
            @if($chat)
                <li class="relative">
                    <a href="{{ route('filament.resources.chat/chats.view', ['record' => $chat->id]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </a>
                    @if($chat->unread_chat_messages_count)
                        <span class="absolute flex justify-center"
                              style="bottom: -10px; right: -10px; width: 20px; height: 20px; font-size: 12px; border-radius: 50%; background-color: red;">
                        {{$chat->unread_chat_messages_count}}
                    </span>
                    @endif
                </li>
                @if($order->status === OrderStatusEnum::CLOSED)
                    <li>
                        <a href="{{ route('admin.order.close', ['order' => $order->id]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
</article>
