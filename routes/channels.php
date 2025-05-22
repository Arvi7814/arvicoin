<?php

use App\Models\Chat\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.User.{user}', function () {
    return true;
});

Broadcast::channel('chats.{chat}', function (App\Models\User\User $user, Chat $chat) {
    return $user->can('update', $chat);
});
