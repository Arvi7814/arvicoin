<?php
declare(strict_types=1);

namespace App\Models\Query;

use App\Enum\OrderStatusEnum;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Order>
 */
class OrderQuery extends Builder
{
    public function whereUser(int $userId): self
    {
        return $this->where('user_id', $userId);
    }

    public function whereStatus(OrderStatusEnum $status): self
    {
        return $this->where('status', $status);
    }

    public function whereNotTelegram(): self
    {
        return $this->where('from_telegram', false);
    }
}
