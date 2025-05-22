<?php

declare(strict_types=1);

namespace App\Exceptions\Sms;

use App\Exceptions\RenderableException;

class SmsAlreadySentException extends RenderableException
{
    public function __construct()
    {
        parent::__construct(trans('messages.sms-already-sent'), 422);
    }
}
