<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = auth('member')->user();

        // باید لاگین باشد (لایه دفاع دوم؛ معمولاً AuthenticateMember قبلاً چک کرده)
        if (! $member) {
            return redirect()->route('panel.login');
        }

        // فقط اعضای تاییدشده به این مسیرها دسترسی دارند
        if ($member->status !== 'approved') {
            return redirect()->route('panel.dashboard')
                ->with('error', 'برای دسترسی به این بخش، حساب شما باید تایید شده باشد.');
        }

        return $next($request);
    }
}
