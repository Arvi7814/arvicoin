<?php

namespace App\Models\Order;

use App\Enum\DeletedStatusEnum;
use App\Enum\OrderStatusEnum;
use App\Enum\RoleEnum;
use App\Models\Chat\Chat;
use App\Models\Query\OrderQuery;
use App\Models\Shop\Product;
use App\Models\User\User;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $operator_id
 * @property string|null $tiktok_login
 * @property string|null $tiktok_password
 * @property string|null $pubg_id
 * @property string|null $currency
 * @property OrderStatusEnum $status
 * @property boolean $from_telegram
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DeletedStatusEnum|null $deleted_status
 *
 * @property-read User $user
 * @property-read User|null $operator
 * @property-read Chat|null $chat
 * @property-read Collection<OrderProduct> $orderProducts
 * @property-read Collection<Product> $products
 *
 * @method static OrderQuery query()
 */
class Order extends Model
{
    protected $guarded = [];
    protected $perPage = 15;

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'closed_at' => 'datetime',
        'from_telegram' => 'boolean',
        'deleted_status' => DeletedStatusEnum::class
    ];

    public function newEloquentBuilder($query): OrderQuery
    {
        return new OrderQuery($query);
    }

    public function delete()
    {
        DB::transaction(function () {
            $user = Auth::user();

            if ($user->hasRole(RoleEnum::MANAGER->value)) {
                $this->deleted_status = DeletedStatusEnum::BY_MANAGER;
            } else if ($user->hasRole(RoleEnum::CUSTOMER->value)) {
                $this->status = OrderStatusEnum::CLOSED;
                $this->deleted_status = DeletedStatusEnum::BY_CUSTOMER;
            } else {
                $this->deleted_status = DeletedStatusEnum::BY_MODERATOR;
            }

            $this->closed_at = now();
            $this->save();
        });
    }

    /**
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * @return BelongsTo<User, Order>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id')->withTrashed();
    }

    /**
     * @return HasOne<Chat>
     */
    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class, 'order_id');
    }


    /**
     * @return HasMany<OrderProduct>
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    /**
     * @return BelongsToMany<Product>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'order_products',
            'order_id',
            'product_id'
        );
    }
}
