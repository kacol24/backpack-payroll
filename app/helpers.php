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

if (!function_exists('calculate_delta_hours')) {
    function calculate_delta_hours ($start, $end) {
        if (! $start instanceof Carbon\Carbon) {
            $start = Carbon\Carbon::parse($start);
        }

        if (! $end instanceof Carbon\Carbon) {
            $end = Carbon\Carbon::parse($end);
        }

        if (! $end) {
            return 0;
        }

        return $start->diffInSeconds($end) / 60 / 60;
    }
}
