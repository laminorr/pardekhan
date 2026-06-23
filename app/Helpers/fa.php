<?php

if (! function_exists('fa')) {
    function fa($value): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $faDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return str_replace($en, $faDigits, (string) $value);
    }
}

if (! function_exists('fanum')) {
    function fanum($value): string
    {
        return fa(number_format((float) $value));
    }
}

if (! function_exists('pdate')) {
    /**
     * تبدیل تاریخ میلادی به شمسی با ارقام فارسی
     * @param mixed  $date   مقدار تاریخ (Carbon یا رشته)
     * @param string $format فرمت جلالی (پیش‌فرض: Y/m/d H:i)
     */
    function pdate($date, string $format = 'Y/m/d H:i'): string
    {
        if (! $date) return '—';
        try {
            $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            return fa(\Morilog\Jalali\Jalalian::fromDateTime($carbon)->format($format));
        } catch (\Throwable $e) {
            return '—';
        }
    }
}
