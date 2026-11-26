<?php

declare(strict_types=1);

if (! function_exists('currency')) {
    function currency(): string
    {
        return 'XAF';
    }
}

if (! function_exists('format_xaf')) {
    function format_xaf(float|int|string $amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' FCFA';
    }
}
