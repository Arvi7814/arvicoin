<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\TelegramException;
use App\Integration\TgLogger;
use App\Models\User\User;
use Illuminate\Http\Request as FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Psy\Util\Json;
use Telegram\Bot\Objects\Audio;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Contact;
use Telegram\Bot\Objects\Document;
use Telegram\Bot\Objects\Location;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\PhotoSize;
use Telegram\Bot\Objects\Video;
use Telegram\Bot\Objects\VideoNote;
use Telegram\Bot\Objects\Voice;
use Throwable;

class WebhookRequest
{
    private ?User $user = null;
    private ?Message $message = null;
    private ?CallbackQuery $callback_query = null;

    public function __construct(
        private readonly FormRequest $request
    )
    {
        $this->setAttributes();

        Log::info(Json::encode($this->message?->toArray() ?? []));
    }

    public function respondable(): bool
    {
        return $this->request->hasAny([
            'message',
            'callback_query'
        ]);
    }

    private function setAttributes(): void
    {
        if ($this->request->has('message')) {
            $this->message = Message::make($this->request->input('message'));
        }

        if ($this->request->has('callback_query')) {
            $this->callback_query = CallbackQuery::make($this->request->input('callback_query'));
        }
    }

    public function getUser(): User
    {
        if (!$this->user) {
            if ($user = User::query()->where('chat_id', $this->chatId())->first()) {
                $this->user = $user;
            } else {
                $sender = $this->sender();

                $this->user = User::create([
                    'first_name' => $sender->firstName,
                    'chat_id' => $this->chatId(),
                    'last_name' => $sender->lastName,
                    'username' => $sender->username
                ]);
            }
        }

        return $this->user;
    }

    /**
     * @throws TelegramException
     */
    public function chatId(): int
    {
        if ($this->message) {
            return $this->message->chat->id;
        }

        if ($this->callback_query) {
            return $this->callback_query->from->id;
        }

        throw new TelegramException('Chat ID does not exists');
    }

    /**
     * @throws TelegramException
     */
    public function messageId(): int
    {
        if ($this->message) {
            return $this->message->messageId;
        }

        if ($this->callback_query) {
            return $this->callback_query->message->messageId;
        }

        throw new TelegramException('Message ID does not exists');
    }

    /**
     * @throws TelegramException
     */
    public function text(): string
    {
        try {
            return $this->message->text;
        } catch (Throwable $e) {
            return '';
        }
    }

    private function sender(): \Telegram\Bot\Objects\User
    {
        if ($this->message) {
            return $this->message->from;
        }

        if ($this->callback_query) {
            return $this->callback_query->from;
        }

        throw new TelegramException('Sender entity does not exists');
    }

    /**
     * @throws TelegramException
     */
    public function callbackData(): string
    {
        try {
            return $this->callback_query->data;
        } catch (Throwable $e) {
            throw new TelegramException('Callback data does not exists');
        }
    }

    public function contact(): Contact
    {
        try {
            return $this->message->contact;
        } catch (Throwable $e) {
            throw new TelegramException('Contact does not exists');
        }
    }

    public function location(): Location
    {
        try {
            return $this->message->location;
        } catch (Throwable $e) {
            throw new TelegramException('Location does not exists');
        }
    }

    /**
     * @return PhotoSize[]|null
     */
    public function photos(): ?Collection
    {
        try {
            return $this->message->photo;
        } catch (Throwable $e) {
        }

        return null;
    }

    public function audio(): ?Audio
    {
        try {
            return $this->message->audio;
        } catch (Throwable $e) {
        }

        return null;
    }

    public function voice(): ?Voice
    {
        try {
            return $this->message->voice;
        } catch (Throwable $e) {
        }

        return null;
    }

    public function video(): ?Video
    {
        try {
            return $this->message->video;
        } catch (Throwable $e) {
        }

        return null;
    }

    public function videoNote(): ?VideoNote
    {
        try {
            return $this->message->videoNote;
        } catch (Throwable $e) {
        }

        return null;
    }

    public function document(): ?Document
    {
        try {
            return $this->message->document;
        } catch (Throwable $e) {
        }

        return null;
    }
}
