<?php
declare(strict_types=1);

namespace App\Http\Services\Chat;

use App\Enum\ChatMessageTypeEnum;
use App\Events\Chat\MessagesDeletedEvent;
use App\Events\Chat\NewMessageEvent;
use App\Http\Requests\WebhookRequest;
use App\Jobs\Chat\AssignFileJob;
use App\Jobs\Chat\DeleteMessageJob;
use App\Jobs\Chat\NotifyOperatorJob;
use App\Jobs\Shop\NotifyTelegramJob;
use App\Models\Chat\Chat;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatUnreadMessage;
use App\Models\User\User;
use Auth;
use DB;
use Telegram\Bot\Objects\PhotoSize;

class MessageService
{
    public function newMessage(Message $message, Chat $chat, User $user): ChatMessage
    {
        return DB::transaction(
            function () use ($message, $chat, $user) {
                $chatMessage = new ChatMessage();
                $chatMessage->chat_id = $chat->id;
                $chatMessage->user_id = $user->id;
                $chatMessage->content = $message->message;
                $chatMessage->type = is_null($message->media) ? ChatMessageTypeEnum::TEXT : ChatMessageTypeEnum::MEDIA;

                $chatMessage->save();

                if ($media = $message->media) {
                    if (str_starts_with($media->path(), 'livewire')) {
                        $chatMessage
                            ->addMediaFromDisk($media->getRealPath(), 's3')
                            ->toMediaCollection();
                    } else {
                        $chatMessage
                            ->addMedia($media)
                            ->toMediaCollection();
                    }
                }

                $order = $chat->order;
                if ($order->from_telegram && $user->id !== $order->user_id) {
                    NotifyTelegramJob::dispatch($order->user_id, $chatMessage);
                } else {
                    broadcast(new NewMessageEvent(
                        chat: $chat,
                        chatMessage: $chatMessage
                    ))->toOthers();
                }

                NotifyOperatorJob::dispatch($chatMessage);

                return $chatMessage;
            }
        );
    }

    public function newMessageFromTelegram(WebhookRequest $request, Chat $chat, User $user): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $chat, $user) {
            $chatMessage = $this->newMessage(
                new Message(message: $request->text(), media: null),
                $chat,
                $user
            );

            $chatMessage->tg_message_id = $request->messageId();
            $chatMessage->save();

            if ($photos = $request->photos()) {
                /** @var ?PhotoSize $mediumPhoto */
                $mediumPhoto = null;

                foreach ($photos as $index => $photo) {
                    if (!$mediumPhoto) {
                        $mediumPhoto = $photo;
                    }

                    if ($mediumPhoto->fileSize < $photo->fileSize) {
                        if ($index + 1 === count($photos)) {
                            continue;
                        }

                        $mediumPhoto = $photo;
                    }
                }

                if ($mediumPhoto) {
                    AssignFileJob::dispatch(
                        $mediumPhoto->fileId,
                        $chatMessage
                    );
                }
            }

            if ($video = $request->video()) {
                AssignFileJob::dispatch(
                    $video->fileId,
                    $chatMessage
                );
            }

            if ($videoNote = $request->videoNote()) {
                AssignFileJob::dispatch(
                    $videoNote->fileId,
                    $chatMessage
                );
            }

            if ($audio = $request->audio()) {
                AssignFileJob::dispatch(
                    $audio->fileId,
                    $chatMessage
                );
            }

            if ($voice = $request->voice()) {
                AssignFileJob::dispatch(
                    $voice->fileId,
                    $chatMessage
                );
            }

            if ($document = $request->document()) {
                AssignFileJob::dispatch(
                    $document->fileId,
                    $chatMessage
                );
            }
        });
    }

    public function deleteMessages(Chat $chat, array $messages): void
    {
        DB::transaction(function () use ($chat, $messages) {
            ChatUnreadMessage::query()
                ->where('chat_id', $chat->id)
                ->whereIn('chat_message_id', $messages)
                ->delete();

            $messages = ChatMessage::query()
                ->where('chat_id', $chat->id)
                ->where('user_id', Auth::id())
                ->whereIn('id', $messages)
                ->get();

            foreach ($messages as $message) {
                DeleteMessageJob::dispatch($chat->order->user, $message);
                $message->delete();
            }
        });

        broadcast(new MessagesDeletedEvent(chat: $chat));
    }
}
