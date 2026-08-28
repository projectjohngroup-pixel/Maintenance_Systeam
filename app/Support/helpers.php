<?php

if (!function_exists('pdsNumber')) {

    /**
     * Format a number for display, stripping unnecessary trailing zeros
     * in the decimal part (e.g. 133,394.00 -> 133,394 ; 12.50 -> 12.5).
     *
     * Adds precision up to 6 decimals when needed so real decimals are kept,
     * but never rounds whole integers. The original value is preserved.
     *
     * @param  mixed  $value
     * @param  string  $decimalSep  Decimal separator (default '.')
     * @param  string  $thousandSep  Thousands separator (default ',')
     */
    function pdsNumber($value, string $decimalSep = '.', string $thousandSep = ',')
    {
        $num = (float) $value;

        $formatted = number_format($num, 6, $decimalSep, $thousandSep);

        if (str_contains($formatted, $decimalSep)) {
            $formatted = rtrim($formatted, '0');
            $formatted = rtrim($formatted, $decimalSep);
        }

        return $formatted;
    }
}

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
