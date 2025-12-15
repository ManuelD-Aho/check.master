<?php

declare(strict_types=1);

namespace App\Utils;

class MoneyHelper
{
    public static function format(float $amount, string $currency = 'FCFA'): string
    {
        // Formatage standard FCFA : 1 000 000 FCFA
        return number_format($amount, 0, ',', ' ') . ' ' . $currency;
    }

    public static function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    public static function fromCents(int $cents): float
    {
        return (float) ($cents / 100);
    }
}
