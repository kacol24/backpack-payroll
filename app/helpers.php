<?php

if (! function_exists('strip_money_mask')) {
    function strip_money_mask($value)
    {
        return str_replace(['.', ','], ['', '.'], $value);
    }
}
