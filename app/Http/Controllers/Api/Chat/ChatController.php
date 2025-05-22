<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chat\CloseChatsRequest;
use App\Http\Requests\Api\Chat\DeleteMessagesRequest;
use App\Http\Requests\Api\Chat\MessageRequest;
use App\Http\Resources\Chat\ChatMessageResource;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Services\Chat\ChatService;
use App\Http\Services\Chat\MessageService;
use App\Jobs\Chat\DeleteUnreadMessagesJob;
use App\Models\Chat\Chat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(
        private readonly MessageService $messageService,
        private readonly ChatService    $chatService,
    )
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return ChatResource::collection(
            Chat::query()
                ->active()
                ->whereMember(Auth::id())
                ->withUnreadMessagesCount(Auth::id())
                ->with(['unreadChatMessages', 'order', 'order.operator'])
                ->paginate()
        );
    }

    public function unread(): AnonymousResourceCollection
    {
        return ChatResource::collection(
            Chat::query()
                ->active()
                ->unread(Auth::id())
                ->whereMember(Auth::id())
                ->withUnreadMessagesCount(Auth::id())
                ->with(['unreadChatMessages', 'order', 'order.operator'])
                ->paginate()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function messages(Chat $chat): AnonymousResourceCollection
    {
        $this->authorize('view', $chat);
        DeleteUnreadMessagesJob::dispatch($chat->id, Auth::id());

        return ChatMessageResource::collection(
            $chat->messages()
                ->latest()
                ->with(['user'])
                ->paginate()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function newMessage(MessageRequest $request, Chat $chat): JsonResponse
    {
        $this->authorize('update', $chat);

        return response()->json(
            ChatMessageResource::make(
                $this->messageService->newMessage(
                    $request->toMessage(),
                    $chat,
                    Auth::user()
                ))
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function closeChats(CloseChatsRequest $request): Response
    {
        $this->chatService->bulkClose($request);

        return response()->noContent();
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteMessages(DeleteMessagesRequest $request, Chat $chat): Response
    {
        $this->messageService->deleteMessages($chat, $request->messages);

        return response()->noContent();
    }
}
