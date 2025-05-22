<?php

namespace App\Models\User;

use App\Enum\LangEnum;
use App\Enum\RoleEnum;
use App\Enum\UserStatus;
use App\Models\Chat\ChatMember;
use App\Models\Order\Order;
use App\Models\Query\UserQuery;
use App\Models\Shop\Cart;
use App\Models\Shop\CartProduct;
use App\Models\Shop\Product;
use App\Models\System\SmsConfirmation;
use App\Telegram\Commands\SendMediaCommand;
use App\Telegram\Commands\SendMediaGroupCommand;
use App\Telegram\Commands\SendMessageCommand;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $password
 * @property LangEnum $language
 * @property ?float $latitude
 * @property ?float $longitude
 * @property ?string $chat_id
 * @property ?string $username
 * @property string $state
 * @property ?string $last_message
 * @property ?string $current_order_id
 * @property UserStatus $status
 * @property Carbon $last_seen
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<UserAccessToken> $accessTokens
 * @property-read Collection<SmsConfirmation> $sms
 * @property-read Cart|null $cart
 * @property-read Order|null $currentOrder
 * @property-read Collection<CartProduct> $cartProducts
 * @property-read Collection<Order> $servedOrders
 * @property-read Collection<Order> $orders
 * @property-read Collection<ChatMember> $memberships
 * @property-read Collection<FcmRegId> $fcmRegIds
 *
 * @method static UserQuery query()
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'language' => LangEnum::class,
        'status' => UserStatus::class
    ];

    public function newEloquentBuilder($query): UserQuery
    {
        return new UserQuery($query);
    }

    public function canAccessFilament(): bool
    {
        return !$this->hasRole(RoleEnum::CUSTOMER->value);
    }

    public function routeNotificationForPusherPushNotifications($notification): string
    {
        return "users.$this->id";
    }

    public function routeNotificationForFcm()
    {
        return $this->fcmRegIds->pluck('token')->toArray();
    }

    public function getFilamentName(): string
    {
        return "$this->last_name $this->first_name";
    }

    /**
     * @return UserAccessToken
     */
    public function getAccessToken(): Model
    {
        return $this->accessTokens()->firstOrCreate([], [
            'token' => Str::random(),
        ]);
    }

    /**
     * @return HasMany<UserAccessToken>
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(UserAccessToken::class, 'user_id');
    }

    /**
     * @return HasMany<SmsConfirmation>
     */
    public function sms(): HasMany
    {
        return $this->hasMany(SmsConfirmation::class, 'user_id');
    }

    /**
     * @return HasOne<Cart>
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    /**
     * @return Cart
     */
    public function createdCart(): Model
    {
        return $this->cart()->firstOrCreate();
    }

    /**
     * @return BelongsTo<Order, User>
     */
    public function currentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }


    public function cartProducts(): HasManyThrough
    {
        return $this->hasManyThrough(CartProduct::class, Cart::class, 'user_id', 'cart_id');
    }

    /**
     * @return HasMany<Order>
     */
    public function servedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'operator_id');
    }

    /**
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * @return HasMany<ChatMember>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ChatMember::class, 'user_id');
    }

    /**
     * @return HasMany<FcmRegId>
     */
    public function fcmRegIds(): HasMany
    {
        return $this->hasMany(FcmRegId::class, 'user_id');
    }

    public function sendMessage(SendMessageCommand $command): void
    {
        if ($command->replaceLastMessage) {
            $this->deleteLastMessage();
        }

        $response = Telegram::bot()->sendMessage(
            $command->params->get($this->chat_id)
        );

        if ($command->nextState) {
            $this->state = $command->nextState;
        }

        $this->last_message = $response->messageId;
        $this->update();
    }

    public function sendMedia(SendMediaCommand $command): void
    {
        if ($command->replaceLastMessage) {
            $this->deleteLastMessage();
        }

        $bot = Telegram::bot();
        $params = $command->params;
        $message = $params->get($this->chat_id);

        if ($params->isPhoto()) {
            $response = $bot->sendPhoto($message);
        } else if ($params->isVideo()) {
            $response = $bot->sendVideo($message);
        } else if ($params->isAnimation()) {
            $response = $bot->sendAnimation($message);
        } else if ($params->isAudio()) {
            $response = $bot->sendAudio($message);
        } else {
            $response = $bot->sendDocument($message);
        }

        if ($command->nextState) {
            $this->state = $command->nextState;
        }

        $this->last_message = $response->messageId;
        $this->update();
    }

    public function sendMediaGroup(SendMediaGroupCommand $command): void
    {
        if ($command->replaceLastMessage) {
            $this->deleteLastMessage();
        }

        $response = Telegram::bot()->sendMediaGroup(
            $command->params->get($this->chat_id)
        );

        if ($command->nextState) {
            $this->state = $command->nextState;
        }

        $this->last_message = $response->messageId;
        $this->update();
    }

    public function deleteLastMessage(): void
    {
        try {
            $this->deleteMessage($this->last_message);
        } catch (Throwable $e) {
        }
    }

    public function deleteMessage(string|int $messageId): void
    {
        Telegram::bot()->deleteMessage([
            'chat_id' => $this->chat_id,
            'message_id' => $messageId
        ]);
    }

    public function getCartProduct(Product $product): CartProduct
    {
        return $this->createdCart()
            ->cartProducts()
            ->firstOrCreate([
                'product_id' => $product->id
            ], ['count' => 1]);
    }

    public function clearCart(): void
    {
        $this->createdCart()->cartProducts()->delete();
    }
}
