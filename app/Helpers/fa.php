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
