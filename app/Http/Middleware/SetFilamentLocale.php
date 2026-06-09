<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentLocale
{
    private const LOCALE = 'fr';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(self::LOCALE);
        Carbon::setLocale(self::LOCALE);
        Number::useLocale(self::LOCALE);

        return $next($request);
    }
}
