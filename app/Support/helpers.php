<?php

if (!function_exists('pdsFormatMinutes')) {

    function pdsFormatMinutes($m)
    {
        $m = (int) $m;

        if ($m <= 0) {
            return '-';
        }

        $days = floor($m / 1440);
        $hours = floor(($m % 1440) / 60);
        $mins = $m % 60;

        $out = '';

        if ($days > 0) {
            $out .= $days . 'h ';
        }

        if ($hours > 0) {
            $out .= $hours . 'j ';
        }

        if ($mins > 0 || $out === '') {
            $out .= $mins . 'm';
        }

        return trim($out);
    }
}
