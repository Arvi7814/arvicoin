<?php

namespace App\Http\Middleware;

use App\Http\Services\ApiAuthService;
use BadMethodCallException;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiAuthMiddleware
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
        if (!$token) {
            throw new BadMethodCallException();
        }

        $service = new ApiAuthService();
        $service->login($token);

        return $next($request);
    }
}
