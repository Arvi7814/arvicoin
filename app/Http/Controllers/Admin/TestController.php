<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Notifications\User\PushMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class TestController
{
    public function __invoke(FormRequest $request)
    {
        $user = Auth::user();

        $user->notify(
            new PushMessage(
                $request->input('title', 'Default title'),
                $request->input('body', 'Default body')
            )
        );
    }
}
