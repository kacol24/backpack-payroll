<?php

if (! function_exists('strip_money_mask')) {
    function strip_money_mask($value)
    {
        return str_replace(['.', ','], ['', '.'], $value);
    }
}

if (! function_exists('format_money')) {
    function format_money($value)
    {
        return 'Rp' . number_format($value, 0, ',', '.');
    }
}
