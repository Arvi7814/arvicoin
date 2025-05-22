<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\SignUpController;
use App\Http\Controllers\Api\Chat\ChatController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Shop\BannerController;
use App\Http\Controllers\Api\Shop\CartController;
use App\Http\Controllers\Api\Shop\ProductController;
use App\Http\Controllers\Api\Shop\TagController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::post('telegram', TelegramController::class);

Route::get('media/{id}', MediaController::class)->name('media.show');

Route::group([
], function () {
    Route::group([
        'prefix' => 'products',
        'as' => 'products.',
    ], static function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });

    Route::group([
        'prefix' => 'tags',
        'as' => 'tags.',
    ], static function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
    });

    Route::group([
        'prefix' => 'banners',
        'as' => 'banners.',
    ], static function () {
        Route::get('/notifications', [BannerController::class, 'notifications'])->name('notifications');
        Route::get('/slides', [BannerController::class, 'slides'])->name('slides');
    });

    Route::group([
        'prefix' => 'auth',
        'as' => 'auth.',
    ], static function () {
        Route::group([
            'prefix' => 'sign-up',
            'as' => 'sign-up.',
        ], static function () {
            Route::middleware('sms.throttle')->group(static function () {
                Route::post('/send-code', [SignUpController::class, 'sendCode'])->name('send-code');
            });
            Route::post('/register', [SignUpController::class, 'register'])->name('register');
        });

        Route::group([
            'prefix' => 'login',
            'as' => 'login.',
        ], static function () {
            Route::middleware('sms.throttle')->group(static function () {
                Route::post('/send-code', [LoginController::class, 'sendCode'])->name('send-code');
            });
            Route::post('/login', [LoginController::class, 'login'])->name('login');
        });
    });
});

Route::group([
    'middleware' => 'auth.api'
], static function () {
    Route::group([
        'prefix' => 'profile',
        'as' => 'profile.'
    ], static function () {
        Route::get('/', [ProfileController::class, 'me'])->name('me');
        Route::post('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'delete'])->name('delete');
    });

    Route::group([
        'prefix' => 'system',
        'as' => 'system.'
    ], static function () {
        Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
    });

    Route::group([
        'prefix' => 'cart',
        'as' => 'cart.'
    ], static function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add-product', [CartController::class, 'addProduct'])->name('add-product');
        Route::post('/sub-product', [CartController::class, 'subProduct'])->name('sub-product');
        Route::delete('/{product}/remove', [CartController::class, 'removeProduct'])->name('remove-product');
        Route::post('/create-order', [CartController::class, 'createOrder'])->name('create-order');
    });

    Route::group([
        'prefix' => 'orders',
        'as' => 'orders.'
    ], static function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    Route::group([
        'prefix' => 'chats',
        'as' => 'chats.'
    ], static function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/unread', [ChatController::class, 'unread'])->name('unread');
        Route::post('/bulk-close', [ChatController::class, 'closeChats'])->name('bulk-close');
        Route::get('/{chat}/messages', [ChatController::class, 'messages'])->name('messages');
        Route::post('/{chat}/message', [ChatController::class, 'newMessage'])->name('new-message');
        Route::delete('/{chat}/messages', [ChatController::class, 'deleteMessages'])->name('delete-messages');
    });

    Route::post('fcm/register', [FcmController::class, 'register']);
});
