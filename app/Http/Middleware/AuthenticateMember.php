<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMember
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('member')->check()) {
            return redirect()->route('panel.login');
        }

        return $next($request);
    }
}
