<?php

if (!function_exists('__')) {
    function __($string)
    {
        $r = trans('messages.'.$string);

        return substr($r, 0, 9) === 'messages.' ? $string : $r;
    }
}
