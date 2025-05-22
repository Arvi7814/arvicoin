<?php

namespace App\Http\Middleware;

use App\Models\System\SmsConfirmation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SmsThrottleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if (null === $ip) {
            throw new BadRequestException();
        }

        $date = now();
        $confirmationsCount = SmsConfirmation::query()
            ->where('ip_address', $ip)
            ->where('created_at', '>', $date->subMinutes(2)->toDateTimeString())
            ->count();

        if ($confirmationsCount > 1) {
            throw new BadRequestException();
        }

        $confirmationsCount = SmsConfirmation::query()
            ->where('ip_address', $ip)
            ->where('created_at', '>', $date->subMinutes(30)->toDateTimeString())
            ->count();

        if ($confirmationsCount > 6) {
            throw new BadRequestException();
        }

        return $next($request);
    }
}
