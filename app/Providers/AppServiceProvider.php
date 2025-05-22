<?php

namespace App\Providers;

use App\Integration\EskizService;
use App\Integration\PilotService;
use App\Models\Order\Order;
use App\Observers\OrderObserver;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Регистрируем внешние сервисы
        $this->app->singleton(EskizService::class, fn () => new EskizService());
        $this->app->singleton(PilotService::class, fn () => new PilotService());
    }

    public function boot(): void
    {
        // Подключаем Telegram-уведомления при создании заказов
        Order::observe(OrderObserver::class);

        // Подключаем скрипты для Filament
        Filament::registerScripts([
            Vite::asset('resources/js/app.js'),
            asset('firebase-messaging-sw.js'),
        ], true);
    }
}
