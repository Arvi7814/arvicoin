<?php

namespace App\Exceptions;

use App\Exceptions\Auth\UserDoesNotExist;
use App\Exceptions\Sms\SmsAlreadySentException;
use App\Exceptions\Sms\WrongCodeException;
use App\Integration\TgLogger;
use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use Psr\Log\LogLevel;
use Throwable;

class Handler extends ExceptionHandler
{
    private TgLogger $logger;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->logger = new TgLogger();
    }

    /**
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        UserDoesNotExist::class,
        SmsAlreadySentException::class,
        WrongCodeException::class,
        BadMethodCallException::class
    ];

    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof RenderableException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                    ], 422);
                } else if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                    ], 422);
                } else if ($e instanceof UnauthorizedException) {
                    return response()->json([
                        'message' => 'Unauthorized',
                    ], 401);
                }

                return App::hasDebugModeEnabled() ? response()->json([
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'trace' => $e->getTrace(),
                ], 500) : response()->json([
                    'message' => 'Internal error occurred',
                ], 500);
            }
        });

        $this->reportable(function (Throwable $e) {
            if ($e instanceof CouldNotSendNotification) {
                $shouldLog = false;
            } else {
                $shouldLog = match ($e->getCode()) {
                    404, 422, 401, 403 => false,
                    default => true
                };
            }


            if ($shouldLog) {
                $this->logger->log($e);
            }
        });
    }
}
