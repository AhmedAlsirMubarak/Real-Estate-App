<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cookie takes priority (set explicitly by the language switch),
        // then session, then the app default.
        // This ensures a stale session value never overrides an explicit switch.
        $locale = $request->cookie('app_locale')
               ?? $request->session()->get('locale')
               ?? config('app.locale', 'ar');

        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = config('app.locale', 'ar');
        }

        // Keep session in sync so flash / redirect->back() carry the right locale
        if ($request->session()->get('locale') !== $locale) {
            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }
}

