<?php

use App\Http\Controllers\FcmController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TestController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
    Route::get('fcm/register', [FcmController::class, 'register']);
    Route::get('order/{order}/close', [OrderController::class, 'close'])->name('order.close');
    Route::get('test', TestController::class);
});

Route::view('privacy-policy', 'privacy-policy');
