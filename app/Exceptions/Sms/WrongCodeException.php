<?php

declare(strict_types=1);

namespace App\Exceptions\Sms;

use App\Exceptions\RenderableException;

class WrongCodeException extends RenderableException
{
    public function __construct()
    {
        parent::__construct(trans('messages.wrong-code'), 422);
    }
}
