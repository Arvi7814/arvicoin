<?php

namespace App\Exceptions\Order;

use App\Exceptions\RenderableException;

class EmptyCartException extends RenderableException
{
    public function __construct()
    {
        parent::__construct(trans('messages.cart-empty'), 422);
    }
}
