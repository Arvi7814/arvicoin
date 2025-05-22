<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Exceptions\RenderableException;

class UserDoesNotExist extends RenderableException
{
    public function __construct()
    {
        parent::__construct(trans('messages.user-does-not-exist'), 422);
    }
}
