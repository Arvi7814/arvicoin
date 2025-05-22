<?php

namespace App\Http\Middleware;

use App\Http\Services\ApiAuthService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            try {
                $service = new ApiAuthService();
                $user = $service->login($token);

                app()->setLocale($user->language->value);
            } catch (UnauthorizedException $e) {
            }
        }

        return $next($request);
    }
}
