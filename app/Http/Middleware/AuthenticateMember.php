<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = auth('member')->user();

        if (! $member) {
            return redirect()->route('panel.login');
        }

        // آخرین بازدیدِ «قبلی» را پیش از هر آپدیت نگه می‌داریم تا داشبورد بتواند
        // مدتِ غیبت را نشان دهد (وگرنه ورودِ فعلی پژمردگی را فوراً پاک می‌کند).
        $request->attributes->set('member_previous_seen', $member->last_seen_at);

        // به‌روزرسانی آخرین بازدید — با throttle حدود ۵ دقیقه تا در هر درخواست ننویسیم
        if ($member->last_seen_at === null || $member->last_seen_at->lt(now()->subMinutes(5))) {
            $member->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
