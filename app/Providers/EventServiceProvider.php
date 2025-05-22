<?php

namespace App\Providers;

use App\Models\Chat\ChatMessage;
use App\Models\Shop\Banner;
use App\Models\Shop\Product;
use App\Models\User\User;
use App\Observers\Chat\ChatMessageObserver;
use App\Observers\Shop\BannerObserver;
use App\Observers\Shop\ProductObserver;
use App\Observers\User\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [];

    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $observers = [
        Banner::class => [BannerObserver::class],
        ChatMessage::class => [ChatMessageObserver::class],
        Product::class => [ProductObserver::class],
        User::class => [UserObserver::class]
    ];

    public function boot(): void
    {
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
